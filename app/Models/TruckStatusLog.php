<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Lịch sử trạng thái xe tải.
 *
 * @property int $id
 * @property int $truck_id
 * @property int $status_id
 * @property \Illuminate\Support\Carbon $date
 * @property float $time_unit
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Truck $truck
 * @property-read \App\Models\TruckStatus $status
 */
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

    /* ---------------- Relationships ---------------- */

    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    public function status()
    {
        return $this->belongsTo(TruckStatus::class, 'status_id');
    }

    /* ---------------- Scopes ---------------- */

    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('date', '=', $date);
    }

    public function scopeForTruck($query, $truckId)
    {
        return $query->where('truck_id', $truckId);
    }
}
