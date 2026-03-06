<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeadsResource extends JsonResource
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
            "name" => $this->name,
            "type_category" => $this->type_category,
            "email" => $this->email,
            "cel_phone" => $this->cel_phone,
            "comments" => $this->comments,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
