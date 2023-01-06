<?php

namespace App\Http\Resources\VisualPaging;

use App\Models\VisualPaging;
use Illuminate\Http\Resources\Json\JsonResource;

class AtlasIEDVisualPagingResource extends JsonResource
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
        'iednet' => $this->iednet
      ];
    }

    public function is_valid_visual_paging_resource() {
      return strval($this->iednet->object_id) === '1200' && $this->iednet->message->id === '2'; 
    }

    public function toVisualPaging() {
      $visualPagingMessage = new VisualPaging();
      $message = $this->iednet->message;
      $object_id = $this->iednet->object_id;
      // Should always start 1200.2.
      $visualPagingMessage->message_id = join('.', [$object_id,$message->id,$message->message_number]);
      $visualPagingMessage->message = $this->get_field_text_by_id(11);
      $visualPagingMessage->duration = '60';
      $visualPagingMessage->priority = '0';
      $visualPagingMessage->zones = $this->get_zones($this->get_field_text_by_id(9));
      return $visualPagingMessage;
    }

    private function get_field_text_by_id($id) {
      $search_index = array_search($id, array_column($this->iednet->message->field, 'field_num'));
      return $search_index === false ? null : $this->iednet->message->field[$search_index]->text;
    }

    private function get_zones($zones_string) {
      if(is_null($zones_string) || trim($zones_string) == '') {
        return '';
      }
      $zones = explode('-', $zones_string);
      $zones = join(',', array_map(function($z) { return trim($z); }, $zones));
      return $zones;
    }
}
