<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Invoice;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Str;

/**
 * Gera o "Pix copia e cola" (BR Code estático, padrão EMV do Banco Central)
 * e o QR Code correspondente. Não depende de API de banco: o pagamento cai
 * direto na chave informada, mas a baixa na fatura continua sendo manual.
 */
class PixService
{
    /**
     * Pix do saldo em aberto de uma fatura (ver recipientFor() para quem recebe).
     *
     * @return array{payload: string, qr_code: string, amount: float, pix_key: string, recipient_name: string}
     * @throws \DomainException com a mensagem para o usuário quando não dá para gerar
     */
    public static function forInvoice(Invoice $invoice)
    {
        $recipient = self::recipientFor($invoice);

        $amount = round((float) $invoice->price - (float) $invoice->total_paid, 2);

        if ($amount <= 0) {
            throw new \DomainException('Esta fatura não tem saldo em aberto.');
        }

        return self::build($recipient, $amount, 'FAT' . $invoice->id);
    }

    /**
     * Um único Pix somando várias faturas (pagamento em lote). Só é permitido quando
     * todas vão para a mesma chave Pix, senão o dinheiro cairia só para um dos recebedores.
     *
     * @param array<int, array{invoice: Invoice, amount: float}> $items valor que será pago em cada fatura
     * @throws \DomainException
     */
    public static function forInvoices(array $items)
    {
        $recipient = null;
        $total = 0;

        foreach ($items as $item) {
            $invoice = $item['invoice'];

            try {
                $current = self::recipientFor($invoice);
            } catch (\DomainException $e) {
                $label = $invoice->name ?: 'Fatura #' . $invoice->id;
                throw new \DomainException("{$label}: {$e->getMessage()}");
            }

            if ($recipient && self::normalizeKey($current['pix_key']) !== self::normalizeKey($recipient['pix_key'])) {
                throw new \DomainException('As faturas selecionadas são de recebedores diferentes. O QR Code Pix em lote só pode ser gerado quando todas vão para a mesma chave Pix.');
            }

            $recipient = $recipient ?? $current;
            $total += (float) $item['amount'];
        }

        $total = round($total, 2);

        if (!$recipient || $total <= 0) {
            throw new \DomainException('Informe o valor a pagar das faturas.');
        }

        $txid = 'FAT' . implode('', array_map(fn ($item) => $item['invoice']->id, $items));

        return self::build($recipient, $total, $txid);
    }

    /**
     * Quem recebe o Pix de uma fatura:
     * - A receber (credit): chave, nome e cidade da própria conta (Configurações), para o cliente pagar.
     * - A pagar (debit): chave do fornecedor vinculado (empresa ou pessoa), para pagar pelo app do banco.
     *
     * @return array{pix_key: string, name: string, city: string}
     */
    private static function recipientFor(Invoice $invoice)
    {
        if ($invoice->status === Invoice::STATUS_CANCELLED) {
            throw new \DomainException('Fatura cancelada.');
        }

        return $invoice->type === 'debit'
            ? self::supplierRecipient($invoice)
            : self::accountRecipient($invoice);
    }

    private static function build(array $recipient, float $amount, string $txid)
    {
        $payload = self::payload($recipient['pix_key'], $recipient['name'], $recipient['city'], $amount, $txid);

        return [
            'payload' => $payload,
            'qr_code' => self::qrCodeDataUri($payload),
            'amount' => $amount,
            'pix_key' => $recipient['pix_key'],
            'recipient_name' => $recipient['name'],
        ];
    }

    private static function accountRecipient(Invoice $invoice)
    {
        $account = Account::find($invoice->account_id);

        if (!$account || !$account->pix_key) {
            throw new \DomainException('Cadastre a chave Pix em Configurações para gerar o QR Code.');
        }

        if (!$account->address_city) {
            throw new \DomainException('Cadastre a cidade da empresa em Configurações para gerar o QR Code.');
        }

        return ['pix_key' => $account->pix_key, 'name' => $account->name, 'city' => $account->address_city];
    }

    /**
     * Empresa fornecedora tem prioridade sobre a pessoa, quando a fatura tem as duas.
     * Nome e cidade no QR estático são só informativos (o app do banco mostra os dados
     * do dono da chave), então a cidade cai para "BRASIL" se o fornecedor não tiver.
     */
    private static function supplierRecipient(Invoice $invoice)
    {
        $company = $invoice->company;
        $lead = $invoice->lead;

        if (!$company && !$lead) {
            throw new \DomainException('Vincule um fornecedor (empresa ou pessoa) à fatura para gerar o QR Code.');
        }

        if ($company && $company->pix_key) {
            return [
                'pix_key' => $company->pix_key,
                'name' => $company->business_name ?: $company->legal_name,
                'city' => $company->city ?: 'BRASIL',
            ];
        }

        if ($lead && $lead->pix_key) {
            return ['pix_key' => $lead->pix_key, 'name' => $lead->name, 'city' => $lead->city ?: 'BRASIL'];
        }

        throw new \DomainException('Cadastre a chave Pix do fornecedor para gerar o QR Code.');
    }

    /**
     * Monta o payload do Pix copia e cola.
     *
     * @param string      $key    Chave Pix (CPF/CNPJ, e-mail, telefone +55... ou chave aleatória)
     * @param string      $name   Nome do recebedor (máx. 25 caracteres)
     * @param string      $city   Cidade do recebedor (máx. 15 caracteres)
     * @param float|null  $amount Valor; null deixa o pagador digitar
     * @param string|null $txid   Identificador que aparece no extrato (máx. 25, alfanumérico)
     */
    public static function payload(string $key, string $name, string $city, ?float $amount = null, ?string $txid = null)
    {
        $merchantAccount = self::field('00', 'br.gov.bcb.pix')
            . self::field('01', self::normalizeKey($key));

        $txid = $txid ? substr(preg_replace('/[^A-Za-z0-9]/', '', $txid), 0, 25) : '';

        $payload = self::field('00', '01')
            . self::field('26', $merchantAccount)
            . self::field('52', '0000')
            . self::field('53', '986')
            . ($amount !== null && $amount > 0 ? self::field('54', number_format($amount, 2, '.', '')) : '')
            . self::field('58', 'BR')
            . self::field('59', self::sanitizeText($name, 25))
            . self::field('60', self::sanitizeText($city, 15))
            . self::field('62', self::field('05', $txid !== '' ? $txid : '***'))
            . '6304';

        return $payload . self::crc16($payload);
    }

    /**
     * QR Code do payload como data URI (PNG em base64), pronto para <img src>.
     */
    public static function qrCodeDataUri(string $payload)
    {
        $options = new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'eccLevel' => 'M',
            'scale' => 8,
            'outputBase64' => true,
        ]);

        return (new QRCode($options))->render($payload);
    }

    private static function field(string $id, string $value)
    {
        return $id . str_pad((string) strlen($value), 2, '0', STR_PAD_LEFT) . $value;
    }

    /**
     * CPF/CNPJ digitados com pontuação viram só números; e-mail vai em minúsculas.
     * Telefone precisa estar no formato +55DDDNUMERO e chave aleatória é mantida como está.
     */
    private static function normalizeKey(string $key)
    {
        $key = trim($key);

        if (preg_match('/^[\d.\-\/ ]+$/', $key)) {
            return preg_replace('/\D/', '', $key);
        }

        if (str_contains($key, '@')) {
            return strtolower($key);
        }

        if (str_starts_with($key, '+')) {
            return '+' . preg_replace('/\D/', '', $key);
        }

        return $key;
    }

    /**
     * Remove acentos e caracteres fora do padrão e corta no tamanho máximo do campo.
     */
    private static function sanitizeText(string $text, int $maxLength)
    {
        $text = Str::ascii($text);
        $text = preg_replace('/[^A-Za-z0-9 .\-]/', '', $text);

        return substr(trim(preg_replace('/\s+/', ' ', $text)), 0, $maxLength);
    }

    /**
     * CRC16-CCITT (polinômio 0x1021, valor inicial 0xFFFF), exigido no campo 63.
     */
    private static function crc16(string $payload)
    {
        $crc = 0xFFFF;

        for ($i = 0; $i < strlen($payload); $i++) {
            $crc ^= ord($payload[$i]) << 8;
            for ($bit = 0; $bit < 8; $bit++) {
                $crc = ($crc & 0x8000) ? (($crc << 1) ^ 0x1021) : ($crc << 1);
                $crc &= 0xFFFF;
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }
}
