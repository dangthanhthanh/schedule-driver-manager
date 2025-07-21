<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $name
 * @property string|null $phone
 * @property int|null $truck_id
 * @property bool $is_main_driver
 * @property string|null $status
 * @property \App\Models\Truck|null $truck
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class DriverResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'phone'          => $this->phone,
            'truck_id'       => $this->truck_id,
            'truck_name'     => $this->truck?->truck_name,
            'is_main_driver' => (bool) $this->is_main_driver,
            'status'         => $this->status,
            'created_at'     => $this->created_at?->toDateTimeString(),
            'updated_at'     => $this->updated_at?->toDateTimeString(),
        ];
    }
}
