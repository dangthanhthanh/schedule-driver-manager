<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Driver
 *
 * @property int $id
 * @property string $name
 * @property string|null $phone
 * @property int|null $truck_id
 * @property bool $is_main_driver
 * @property Truck|null $truck
 * @property-read \Illuminate\Database\Eloquent\Collection|Schedule[] $schedules
 * @property-read \Illuminate\Database\Eloquent\Collection|ScheduleTruck[] $scheduleTrucks
 * @property-read \Illuminate\Database\Eloquent\Collection|DriverStatus[] $statuses
 * @property-read \Illuminate\Database\Eloquent\Collection|DriverStatusLog[] $statusLogs
 */
class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'truck_id',
        'is_main_driver',
    ];

    protected $casts = [
        'is_main_driver' => 'boolean',
    ];

    // Relationships

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class)->withDefault();
    }

    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(Schedule::class, 'schedule_trucks')
            ->withPivot(['truck_id', 'from_location_id', 'to_location_id', 'assistant', 'cargo_desc'])
            ->withTimestamps();
    }

    public function scheduleTrucks(): HasMany
    {
        return $this->hasMany(ScheduleTruck::class, 'driver_id');
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(DriverStatus::class, 'driver_status_log', 'driver_id', 'status_id')
            ->withPivot(['date', 'time_unit'])
            ->withTimestamps();
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(DriverStatusLog::class, 'driver_id');
    }

    // Query Scopes

    public function scopeMain($query)
    {
        return $query->where('is_main_driver', true);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('truck_id');
    }
}