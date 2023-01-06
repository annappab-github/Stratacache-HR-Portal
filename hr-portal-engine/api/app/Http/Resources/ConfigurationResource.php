<?php

namespace App\Http\Resources;

use App\Models\DataType;
use Illuminate\Http\Resources\Json\JsonResource;

class ConfigurationResource extends JsonResource
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
          'id' => $this->key,
          'name' => $this->name,
          'description' => $this->description,
          'value' => $this->value,
          'type' => (DataType::where('id', $this->data_type_id)->first())->type,
        ];
    }
}
