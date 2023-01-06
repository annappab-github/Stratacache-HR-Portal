<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SimplewayAirportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      // "airport": {"code": "PDX",
      //   "city": "PORTLAND",
      //   "state": "OR"},
      return [
        'code' => isset($this->code) ? $this->code : null,
        'city' => isset($this->city) ? $this->city : null,
        'state' => isset($this->state_id) ? $this->state_id : null,
      ];
    }
}
