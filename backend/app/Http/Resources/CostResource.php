<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CostResource extends JsonResource
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
            'company_id' => $this->account_id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'pivot_price' => $this->whenPivotLoaded('service_costs', function () {
                return $this->pivot->price;
            }),
            'quantity' => $this->whenPivotLoaded('service_costs', function () {
                return $this->pivot->quantity;
            }),
            'pivot_total_price' => $this->whenPivotLoaded('service_costs', function () {
                return $this->pivot->total_price;
            }),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'deleted_at' => $this->deleted_at ? $this->deleted_at->toDateTimeString() : null,
        ];
    }
}
