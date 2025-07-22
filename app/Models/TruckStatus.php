<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TruckStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
    ];

    // Relationships

    public function trucks(): BelongsToMany
    {
        return $this->belongsToMany(Truck::class, 'truck_status_log', 'status_id', 'truck_id')
            ->withPivot(['date', 'time_unit'])
            ->withTimestamps();
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(TruckStatusLog::class, 'status_id');
    }
}