<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Truck
 *
 * @property int $id
 * @property string $truck_name
 * @property string|null $status
 * @property int|null $project
 * @property int|null $floor
 * @property int|null $capacity
 * @property string|null $description
 * @property-read \Illuminate\Database\Eloquent\Collection|Driver[] $drivers
 * @property-read \Illuminate\Database\Eloquent\Collection|ScheduleTruck[] $scheduleTrucks
 * @property-read \Illuminate\Database\Eloquent\Collection|TruckStatus[] $statuses
 * @property-read \Illuminate\Database\Eloquent\Collection|TruckStatusLog[] $statusLogs
 */
class Truck extends Model
{
    use HasFactory;

    protected $fillable = [
        'truck_name',
        'status',
        'project',
        'floor',
        'capacity',
        'description',
    ];

    // Relationships

    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class, 'truck_id');
    }

    public function scheduleTrucks(): HasMany
    {
        return $this->hasMany(ScheduleTruck::class, 'truck_id');
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(TruckStatus::class, 'truck_status_log', 'truck_id', 'status_id')
            ->withPivot(['date', 'time_unit'])
            ->withTimestamps();
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(TruckStatusLog::class, 'truck_id');
    }

    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(Schedule::class, 'schedule_trucks')
            ->withPivot(['driver_id', 'from_location_id', 'to_location_id', 'assistant', 'cargo_desc'])
            ->withTimestamps();
    }

    // Query Scopes

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'bảo trì')->orWhereNull('status');
    }

    public function scopeCapacityAtLeast($query, $minCapacity)
    {
        return $query->where('capacity', '>=', $minCapacity);
    }
}