<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'category' => $this->category,
			"name" => $this->name,
            'labor_hours' => $this->labor_hours,
            'labor_hourly_rate' => $this->labor_hourly_rate,
            'labor_hourly_total' => $this->labor_hourly_total,
            'profit_percentage' => $this->profit_percentage,
            "price" => $this->price,
            'observations' => $this->observations,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at,
			];
    }
}
