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
            "email" => $this->email,
            "cel_phone" => $this->cel_phone,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
