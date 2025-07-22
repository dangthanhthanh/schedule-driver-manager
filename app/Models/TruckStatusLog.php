<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TruckStatusLog extends Model
{
    use HasFactory;

    protected $table = 'truck_status_logs';

    protected $fillable = [
        'truck_id',
        'status_id',
        'date',
        'time_unit',
    ];

    protected $casts = [
        'date'      => 'date',
        'time_unit' => 'float',
    ];

    // Relationships

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class, 'truck_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TruckStatus::class, 'status_id');
    }

    // Scopes

    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('date', '=', $date);
    }

    public function scopeForTruck($query, $truckId)
    {
        return $query->where('truck_id', $truckId);
    }
}