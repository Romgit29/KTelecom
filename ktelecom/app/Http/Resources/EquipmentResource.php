<?php

namespace App\Http\Resources;

use App\Models\EquipmentType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    // new \App\Http\Resources\EquipmentCollection( \App\Models\Equipment::whereIn('id', [1])->get() )
    // (new \App\Http\Resources\EquipmentResource(request()))::collection( \App\Models\Equipment::whereIn('id', [1])->get() )
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'equipment_type' => new EquipmentTypeResource(EquipmentType::find( $this->equipment_type_id )),
            'serial_number' => $this->serial_number,
            'comment' => $this->comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
        // json_encode(\App\Http\Resources\EquipmentResource::collection(\App\Models\Equipment::all()));
    }
}
