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
       	return $data = [
			"id" => $this->id,
			"name" => $this->name,
			"email" => $this->email,
			"cel_phone" => $this->cel_phone,
			"comments" => $this->comments,
			"trash" => $this->trash,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at,
			];
    }
}        