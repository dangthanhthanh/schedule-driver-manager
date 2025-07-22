<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverStatusLog extends Model
{
    use HasFactory;

    protected $table = 'driver_status_logs';

    protected $fillable = [
        'driver_id',
        'status_id',
        'date',
        'time_unit',
    ];

    protected $casts = [
        'date'      => 'date',
        'time_unit' => 'float',
    ];

    // Relationships

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(DriverStatus::class, 'status_id');
    }

    // Scopes

    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('date', '=', $date);
    }

    public function scopeForDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }
}