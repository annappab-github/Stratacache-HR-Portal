<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class VisualPagingResourceCollection extends ResourceCollection
{
    private $default_duration;

    public function __construct($resource, $default_duration = 0)
    {
        // Ensure you call the parent constructor
        parent::__construct($resource);  
        $this->default_duration = $default_duration;
    }
    
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
          'queue-items' => $this->collection->map(function($r) {
            return new VisualPagingResource($r, $this->default_duration);
           })
        ];
    }
}