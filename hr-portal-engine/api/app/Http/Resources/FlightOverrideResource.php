<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FlightOverrideResource extends JsonResource
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
        "uid" => $this->uid,
        "estimated" => $this->estimated_flight_time,
        "gate" => $this->gate,
        "claim" => $this->baggage,
        "checkIn" => null,
        "status" => $this->status,
        "languages" => null,
        "remarks" => $this->remarks,
        "isUnbound" => (bool)$this->is_custom,
      ];
    }
}
