<?php

namespace App\Services;

use App\Models\Account;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

/**
 * Guarda o certificado digital A1 (e-CNPJ, arquivo .pfx) usado para assinar e transmitir a NFS-e.
 *
 * O arquivo fica no disco "local" (storage/app, fora da pasta pública e do git) e a senha
 * é gravada criptografada (cast "encrypted" no model Account).
 */
class NfseCertificateService
{
    private const DIRECTORY = 'nfse/certificates';

    /**
     * Valida o certificado e grava na conta.
     *
     * @throws \DomainException com a mensagem para o usuário quando o certificado não serve
     */
    public static function store(Account $account, UploadedFile $file, string $password): Account
    {
        $pfx = file_get_contents($file->getRealPath());
        $certificates = self::read($pfx, $password);

        if ($certificates === null) {
            // Muitos certificados A1 ainda vêm com criptografia antiga (RC2-40), que o OpenSSL 3
            // não abre por padrão. Nesse caso convertemos para um .pfx moderno com a mesma senha.
            $pfx = self::convertLegacyPfx($pfx, $password);
            $certificates = $pfx ? self::read($pfx, $password) : null;
        }

        if ($certificates === null) {
            throw new \DomainException('Não foi possível abrir o certificado. Confira a senha e se o arquivo é um certificado A1 (.pfx ou .p12).');
        }

        $info = openssl_x509_parse($certificates['cert']);
        if (!$info) {
            throw new \DomainException('O arquivo não contém um certificado válido.');
        }

        $expiresAt = \Carbon\Carbon::createFromTimestamp($info['validTo_time_t']);
        if ($expiresAt->isPast()) {
            throw new \DomainException('Este certificado venceu em ' . $expiresAt->format('d/m/Y') . '.');
        }

        // No e-CNPJ o nome comum vem como "RAZAO SOCIAL:CNPJ"
        $holder = $info['subject']['CN'] ?? '';
        if (is_array($holder)) {
            $holder = implode(' ', $holder);
        }
        $certificateCnpj = preg_match('/:(\d{14})$/', $holder, $matches) ? $matches[1] : null;
        $accountCnpj = preg_replace('/\D/', '', (string) $account->cnpj);

        if ($certificateCnpj && $accountCnpj && $certificateCnpj !== $accountCnpj) {
            throw new \DomainException('Este certificado é do CNPJ ' . $certificateCnpj . ', diferente do CNPJ cadastrado na conta.');
        }

        $path = self::DIRECTORY . '/account_' . $account->id . '.pfx';
        Storage::disk('local')->put($path, $pfx);

        $account->nfse_certificate_path = $path;
        $account->nfse_certificate_password = $password;
        $account->nfse_certificate_holder = $holder ?: null;
        $account->nfse_certificate_expires_at = $expiresAt;
        $account->save();

        return $account;
    }

    public static function remove(Account $account): Account
    {
        if ($account->nfse_certificate_path) {
            Storage::disk('local')->delete($account->nfse_certificate_path);
        }

        $account->nfse_certificate_path = null;
        $account->nfse_certificate_password = null;
        $account->nfse_certificate_holder = null;
        $account->nfse_certificate_expires_at = null;
        $account->save();

        return $account;
    }

    private static function read(string $pfx, string $password): ?array
    {
        $certificates = [];

        return openssl_pkcs12_read($pfx, $certificates, $password) ? $certificates : null;
    }

    /**
     * Abre o .pfx com o provedor "legacy" do OpenSSL (via linha de comando) e exporta
     * de novo pelo PHP, que usa os algoritmos atuais. Retorna null se não conseguir.
     */
    private static function convertLegacyPfx(string $pfx, string $password): ?string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'pfx');
        file_put_contents($tempFile, $pfx);

        try {
            $process = new Process(
                ['openssl', 'pkcs12', '-in', $tempFile, '-nodes', '-legacy', '-passin', 'env:PFX_PASSWORD'],
                null,
                ['PFX_PASSWORD' => $password]
            );
            $process->run();

            if (!$process->isSuccessful()) {
                return null;
            }

            $pem = $process->getOutput();
            $privateKey = openssl_pkey_get_private($pem);
            preg_match_all('/-----BEGIN CERTIFICATE-----.+?-----END CERTIFICATE-----/s', $pem, $matches);
            if (!$privateKey || empty($matches[0])) {
                return null;
            }

            // O certificado do titular é o que corresponde à chave privada; os demais são da cadeia
            $holderCertificate = null;
            $chain = [];
            foreach ($matches[0] as $certificate) {
                if (!$holderCertificate && openssl_x509_check_private_key($certificate, $privateKey)) {
                    $holderCertificate = $certificate;
                } else {
                    $chain[] = $certificate;
                }
            }
            if (!$holderCertificate) {
                return null;
            }

            $converted = null;
            $exported = openssl_pkcs12_export($holderCertificate, $converted, $privateKey, $password, $chain ? ['extracerts' => $chain] : []);

            return $exported ? $converted : null;
        } catch (\Throwable $exception) {
            return null;
        } finally {
            @unlink($tempFile);
        }
    }
}
