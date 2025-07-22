<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DriverStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
    ];

    // Relationships

    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(Driver::class, 'driver_status_log', 'status_id', 'driver_id')
            ->withPivot(['date', 'time_unit'])
            ->withTimestamps();
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(DriverStatusLog::class, 'status_id');
    }
}