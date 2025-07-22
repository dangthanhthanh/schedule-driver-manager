<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Schedule
 *
 * @property int $id
 * @property int $schedule_id
 * @property int $truck_id
 * @property int $driver_id
 * @property int $from_location_id
 * @property int $to_location_id
 * @property string|null $assistant
 * @property string|null $cargo_desc
 */
class ScheduleTruck extends Model
{
    use HasFactory;

    protected $table = 'schedule_trucks';

    protected $fillable = [
        'schedule_id',
        'truck_id',
        'driver_id',
        'from_location_id',
        'to_location_id',
        'assistant',
        'cargo_desc',
    ];

    // Relationships

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class, 'truck_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }
}