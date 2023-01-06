<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AirportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
          "code" => $this->code,
          "codeType" => $this->code_type,
          "name" => $this->name,
          "city" => $this->city,
          "state" => $this->state_id,
          "country" => $this->country_id,
        ];
    }
}
