<?php

namespace App\Http\Resources;

use App\Services\DateTimeConversionService;
use Illuminate\Http\Resources\Json\JsonResource;

class NfseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $timezone = auth()->user()->timezone ?? 'America/Sao_Paulo';

        return [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'is_manual' => $this->is_manual,
            'status' => $this->status,
            'environment' => $this->environment,
            'nfse_number' => $this->nfse_number,
            'access_key' => $this->access_key,
            'amount' => $this->amount,
            'description' => $this->description,
            'competence_date' => $this->competence_date?->format('Y-m-d'),
            // Só a data, já no fuso do usuário (a hora não aparece na fatura)
            'issued_date' => $this->issued_at
                ? substr(DateTimeConversionService::convertFromUtc($this->issued_at, $timezone), 0, 10)
                : null,
            'error_message' => $this->error_message,
            'cancelled_at' => $this->cancelled_at,
            'cancellation_reason' => $this->cancellation_reason,
            'has_pdf' => (bool) $this->pdf_path,
            'created_at' => $this->created_at,
            'invoice' => $this->whenLoaded('invoice', function () {
                if (!$this->invoice) {
                    return null;
                }

                return [
                    'id' => $this->invoice->id,
                    'name' => $this->invoice->name,
                    'client_name' => $this->invoice->company?->business_name
                        ?? $this->invoice->company?->legal_name
                        ?? $this->invoice->lead?->name
                        ?? null,
                ];
            }),
        ];
    }
}
