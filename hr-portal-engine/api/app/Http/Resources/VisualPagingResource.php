<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VisualPagingResource extends JsonResource
{
  private $default_duration;

  public function __construct($resource, $default_duration = 0)
  {
      // Ensure you call the parent constructor
      parent::__construct($resource);  
      $this->default_duration = $default_duration;
  }

    /**
    * Transform the resource into an array.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return array
    */
   public function toArray($request)
   {
     return [
       'id' => $this->message_id, // not used, we can send it anyway
       'text' => $this->message,
       'priority' => $this->priority,
       'duration' => $this->get_duration(),
       'zones' => $this->get_zones($this->zones),
     ];
   }

   private function get_duration() {
     if (isset($this->duration) && is_numeric($this->duration) && $this->duration > 0) return $this->duration;
     return $this->default_duration;
   }

   public static function collection($resource, $default_duration = 0){
       return new VisualPagingResourceCollection($resource, $default_duration);
   }

   private function get_zones($zones_string) {
     if(is_null($zones_string) || trim($zones_string) == '') {
       return '';
     }
     $zones = explode(',', $zones_string);
     $zones = 'Zone ' . join(',Zone ', array_map(function($z) { return trim($z); }, $zones));
     return $zones;
   }
}
