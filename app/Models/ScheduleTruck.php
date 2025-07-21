<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model trung gian cho nhiều xe/driver trong một lịch trình.
 *
 * @property int $id
 * @property int $schedule_id
 * @property int|null $truck_id
 * @property int|null $driver_id
 * @property int|null $from_location_id
 * @property int|null $to_location_id
 * @property string|null $assistant
 * @property string|null $cargo_desc
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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

    /* ---------------- Relationships ---------------- */

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }
}
