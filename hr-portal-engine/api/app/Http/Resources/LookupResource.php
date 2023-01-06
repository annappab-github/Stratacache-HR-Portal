<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LookupResource extends JsonResource
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
          "key" => $this->lookup,
          "name" => $this->display_name,
          "isActive" => $this->is_active,
          "sortOrder" => $this->sort_order,
        ];
    }
}
