<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'logo' => $this->logo,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'address_city' => $this->address_city,
            'zip_code' => $this->zip_code,
            'ibge_city_code' => $this->ibge_city_code,
            'cnpj' => $this->cnpj,
            'is_mei' => $this->is_mei,
            'mei_annual_limit' => $this->mei_annual_limit,
            'inscricao_municipal' => $this->inscricao_municipal,
            'nfse_environment' => $this->nfse_environment,
            'nfse_default_service_code' => $this->nfse_default_service_code,
            'nfse_dps_series' => $this->nfse_dps_series,
            'nfse_next_dps_number' => $this->nfse_next_dps_number,
            // O certificado em si (arquivo e senha) nunca sai da API; só o status
            'nfse_certificate' => [
                'uploaded' => $this->hasNfseCertificate(),
                'holder' => $this->nfse_certificate_holder,
                'expires_at' => $this->nfse_certificate_expires_at,
            ],
            'pix_key' => $this->pix_key,
            'theme_preference' => $this->theme_preference,
        ];
    }
}
