<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Driver
 *
 * @property int $id
 * @property string $name
 * @property string|null $phone
 * @property int|null $truck_id
 * @property bool $is_main_driver
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Truck|null $truck
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Schedule[] $schedules
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ScheduleTruck[] $scheduleTrucks
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DriverStatus[] $statuses
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DriverStatusLog[] $statusLogs
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

    /* -------------------------------------------------
     | Relationships
     |------------------------------------------------- */

    /**
     * Xe mà tài xế đang gán (nullable).
     */
    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    /**
     * Các lịch trình mà tài xế là tài xế chính (cột schedules.driver_id).
     */
    public function schedules()
    {
        return $this->belongsToMany(Schedule::class, 'schedule_trucks')
                    ->withPivot(['truck_id', 'from_location_id', 'to_location_id', 'assistant', 'cargo_desc'])
                    ->withTimestamps();
    }

    /**
     * Các record trong bảng schedule_trucks nơi tài xế này tham gia
     * (dùng khi 1 lịch có nhiều xe/tài xế).
     */
    public function scheduleTrucks()
    {
        return $this->hasMany(ScheduleTruck::class);
    }

    /**
     * Các trạng thái gán cho tài xế (nhiều-nhiều) qua bảng driver_status_logs.
     * Pivot gồm: date, time_unit.
     */
    public function statuses()
    {
        return $this->belongsToMany(DriverStatus::class, 'driver_status_log', 'driver_id', 'status_id')
                    ->withPivot(['date', 'time_unit'])
                    ->withTimestamps();
    }

    /**
     * Truy cập trực tiếp bảng log (nếu cần truy vấn lịch sử nhanh theo ngày).
     */
    public function statusLogs()
    {
        return $this->hasMany(DriverStatusLog::class);
    }

    /* -------------------------------------------------
     | Query Scopes tiện dụng
     |------------------------------------------------- */

    /**
     * Lọc chỉ tài xế chính.
     */
    public function scopeMain($query)
    {
        return $query->where('is_main_driver', true);
    }

    /**
     * Lọc tài xế đang không gán xe.
     */
    public function scopeUnassigned($query)
    {
        return $query->whereNull('truck_id');
    }
}
