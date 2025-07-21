<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Ghi nhận trạng thái của tài xế theo ngày/ca.
 *
 * @property int $id
 * @property int $driver_id
 * @property int $status_id
 * @property \Illuminate\Support\Carbon $date
 * @property float $time_unit   // 1 = nguyên ngày; 0.5 = nửa ngày
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Driver $driver
 * @property-read \App\Models\DriverStatus $status
 */
class DriverStatusLog extends Model
{
    use HasFactory;

    protected $table = 'driver_status_logs'; // MIGRATION DÙNG SINGULAR

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

    /* ---------------- Relations ---------------- */

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function status()
    {
        return $this->belongsTo(DriverStatus::class, 'status_id');
    }

    /* ---------------- Scopes ---------------- */

    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('date', '=', $date);
    }

    public function scopeForDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }
}
