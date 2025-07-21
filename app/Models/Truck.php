<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Driver[] $drivers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ScheduleTruck[] $scheduleTrucks
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TruckStatus[] $statuses
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TruckStatusLog[] $statusLogs
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

    /* -------------------------------------------------
     | Relationships
     |------------------------------------------------- */

    /**
     * Các tài xế đang được gán cho xe này (1 xe có nhiều tài xế).
     */
    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    /**
     * Lịch trình có liên quan tới xe này qua bảng schedule_trucks.
     */
    public function scheduleTrucks()
    {
        return $this->hasMany(ScheduleTruck::class);
    }

    /**
     * Các trạng thái của xe (mối quan hệ nhiều-nhiều) thông qua truck_status_logs.
     * Pivot chứa: date, time_unit.
     */
    public function statuses()
    {
        return $this->belongsToMany(TruckStatus::class, 'truck_status_log', 'truck_id', 'status_id')
                    ->withPivot(['date', 'time_unit'])
                    ->withTimestamps();
    }

    /**
     * Truy cập trực tiếp log trạng thái của xe.
     */
    public function statusLogs()
    {
        return $this->hasMany(TruckStatusLog::class);
    }

    /* -------------------------------------------------
     | Query Scopes tiện dụng
     |------------------------------------------------- */

    /**
     * Lọc xe đang hoạt động (không bảo trì).
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'bảo trì')->orWhereNull('status');
    }

    /**
     * Lọc xe theo tải trọng lớn hơn hoặc bằng.
     */
    public function scopeCapacityAtLeast($query, $minCapacity)
    {
        return $query->where('capacity', '>=', $minCapacity);
    }

    public function schedules()
    {
        return $this->belongsToMany(Schedule::class, 'schedule_trucks')
                    ->withPivot(['driver_id', 'from_location_id', 'to_location_id', 'assistant', 'cargo_desc'])
                    ->withTimestamps();
    }
}
