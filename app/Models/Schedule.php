<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Schedule
 *
 * @property int $id
 * @property string|null $title
 * @property \Illuminate\Support\Carbon|null $date
 * @property string|null $general_note
 * @property-read \Illuminate\Database\Eloquent\Collection|Truck[] $trucks
 * @property-read \Illuminate\Database\Eloquent\Collection|Driver[] $drivers
 * @property-read \Illuminate\Database\Eloquent\Collection|ScheduleTruck[] $scheduleTrucks
 */
class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'date',
        'general_note',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Relationships

    public function trucks(): BelongsToMany
    {
        return $this->belongsToMany(Truck::class, 'schedule_trucks')
            ->withPivot(['driver_id', 'from_location_id', 'to_location_id', 'assistant', 'cargo_desc'])
            ->withTimestamps();
    }

    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(Driver::class, 'schedule_trucks')
            ->withPivot(['truck_id', 'helper_name', 'load_detail'])
            ->withTimestamps();
    }

    public function scheduleTrucks(): HasMany
    {
        return $this->hasMany(ScheduleTruck::class, 'schedule_id');
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'schedule_locations')
            ->withPivot(['type'])
            ->withTimestamps();
    }

    // Query Scopes

    public function scopeToday($query)
    {
        return $query->whereDate('date', now()->toDateString());
    }
}