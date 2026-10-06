<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NfseResource;
use App\Models\Invoice;
use App\Models\Nfse;
use App\Services\DateTimeConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Notas fiscais de serviço ligadas às faturas.
 *
 * Por enquanto só o registro manual: nota emitida fora do sistema (ex: no Emissor Nacional)
 * e anotada na fatura com número, chave de acesso, data e PDF. A emissão automática
 * pela Sefin Nacional vai gravar na mesma tabela, então as duas aparecem igual na fatura.
 */
class NfseController extends Controller
{
    private const PDF_DIRECTORY = 'nfse/pdfs';

    /**
     * Registra na fatura uma nota emitida fora do sistema
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeManual(Request $request, Invoice $invoice)
    {
        // A chave costuma ser copiada do PDF com espaços ou pontos; guarda só os dígitos
        if ($request->filled('access_key')) {
            $request->merge(['access_key' => preg_replace('/\D/', '', $request->input('access_key')) ?: null]);
        }

        $validated = $request->validate([
            'nfse_number' => 'required|string|max:20',
            'access_key' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('nfses', 'access_key'),
            ],
            'issued_date' => 'required|date_format:Y-m-d',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'pdf' => 'nullable|file|mimes:pdf|max:5120',
        ], [
            'nfse_number.required' => 'Informe o número da nota.',
            'nfse_number.max' => 'O número da nota pode ter no máximo 20 caracteres.',
            'access_key.max' => 'A chave de acesso pode ter no máximo 50 caracteres.',
            'access_key.unique' => 'Já existe uma nota registrada com esta chave de acesso.',
            'issued_date.required' => 'Informe a data de emissão.',
            'issued_date.date_format' => 'Data de emissão inválida.',
            'amount.required' => 'Informe o valor da nota.',
            'amount.min' => 'O valor da nota deve ser maior que zero.',
            'pdf.mimes' => 'O arquivo da nota deve ser um PDF.',
            'pdf.max' => 'O PDF da nota não pode passar de 5MB.',
        ]);

        $timezone = auth()->user()->timezone ?? 'America/Sao_Paulo';

        $nfse = new Nfse([
            'invoice_id' => $invoice->id,
            'user_id' => auth()->id(),
            'is_manual' => true,
            'status' => Nfse::STATUS_AUTHORIZED,
            'nfse_number' => $validated['nfse_number'],
            'access_key' => $validated['access_key'] ?? null,
            'amount' => $validated['amount'],
            'description' => $validated['description'] ?? null,
            'competence_date' => $validated['issued_date'],
            // Meio-dia no fuso do usuário, para a data não mudar ao converter para UTC
            'issued_at' => DateTimeConversionService::convertToUtc($validated['issued_date'] . ' 12:00:00', $timezone),
        ]);
        $nfse->save();

        if ($request->hasFile('pdf')) {
            $nfse->pdf_path = $request->file('pdf')->storeAs(
                self::PDF_DIRECTORY . '/account_' . $nfse->account_id,
                'nfse_' . $nfse->id . '.pdf',
                'local'
            );
            $nfse->save();
        }

        return NfseResource::make($nfse)->response()->setStatusCode(201);
    }

    /**
     * Abre o PDF da nota (fica fora da pasta pública)
     *
     * @param  \App\Models\Nfse  $nfse
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\JsonResponse
     */
    public function pdf(Nfse $nfse)
    {
        if (!$nfse->pdf_path || !Storage::disk('local')->exists($nfse->pdf_path)) {
            return response()->json(['message' => 'Esta nota não tem PDF anexado.'], 404);
        }

        $fileName = 'NFS-e ' . ($nfse->nfse_number ?: $nfse->id) . '.pdf';

        return Storage::disk('local')->response($nfse->pdf_path, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Apaga uma nota registrada manualmente (ex: digitada errado ou na fatura errada).
     * Notas emitidas pelo sistema não são apagadas: precisam ser canceladas na Sefin.
     *
     * @param  \App\Models\Nfse  $nfse
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Nfse $nfse)
    {
        if (!$nfse->is_manual) {
            return response()->json([
                'message' => 'Notas emitidas pelo sistema não podem ser apagadas, apenas canceladas.',
            ], 422);
        }

        if ($nfse->pdf_path) {
            Storage::disk('local')->delete($nfse->pdf_path);
        }
        $nfse->delete();

        return response()->json(null, 204);
    }
}
