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
            'cnpj' => $this->cnpj,
            'is_mei' => $this->is_mei,
            'mei_annual_limit' => $this->mei_annual_limit,
            'inscricao_municipal' => $this->inscricao_municipal,
        ];
    }
}
