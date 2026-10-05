<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            "id" => $this->id,
            "legal_name" => $this->legal_name,
            "business_name" => $this->business_name,
            "photo" => $this->photo,
            "cnpj" => $this->cnpj,
            "email" => $this->email,
            "phone" => $this->phone,
            "cel_phone" => $this->cel_phone,
            "pix_key" => $this->pix_key,
            "linkedin" => $this->linkedin,
            "facebook" => $this->facebook,
            "address" => $this->address,
            "complement" => $this->complement,
            "neighborhood" => $this->neighborhood,
            "city" => $this->city,
            "state" => $this->state,
            "country" => $this->country,
            "zip_code" => $this->zip_code,
            "ibge_city_code" => $this->ibge_city_code,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
