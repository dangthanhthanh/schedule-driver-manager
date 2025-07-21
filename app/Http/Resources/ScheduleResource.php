<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string|null $project
 * @property int|null $driverId
 * @property int|null $truckId
 * @property string|null $date
 * @property string|null $start_time
 * @property string|null $end_time
 * @property string|null $from
 * @property string|null $to
 * @property string|null $status
 * @property \App\Models\Driver|null $driver
 * @property \App\Models\Truck|null $truck
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'project'    => $this->project,
            'driver_id'  => $this->driverId,
            'driver_name'=> $this->driver?->name,
            'truck_id'   => $this->truckId,
            'truck_name' => $this->truck?->truck_name,
            'date'       => $this->date,
            'start_time' => $this->start_time,
            'end_time'   => $this->end_time,
            'from'       => $this->from,
            'to'         => $this->to,
            'status'     => $this->status,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
