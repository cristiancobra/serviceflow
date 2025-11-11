<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
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
            'account_id' => $this->account_id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'email' => $this->email,
            'cel_phone' => $this->cel_phone,
            'linkedin' => $this->linkedin,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'other_social_media' => $this->other_social_media,
            'address' => $this->address,
            'address_complement' => $this->address_complement,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'zip_code' => $this->zip_code,
            'contact_date' => $this->contact_date,
            'source' => $this->source,
            'source_contact_channel' => $this->source_contact_channel,
            'reason_for_initial_contact' => $this->reason_for_initial_contact,
            'comments' => $this->comments,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            
            // relationships
            'opportunities' => OpportunitiesResource::collection($this->whenLoaded('opportunities')),
        ];
    }
}
