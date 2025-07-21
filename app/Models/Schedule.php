<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Schedule
 *
 * @property int $id
 * @property string|null $title
 * @property \Illuminate\Support\Carbon|null $date
 * @property string|null $general_note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Truck[] $trucks
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Driver[] $drivers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ScheduleTruck[] $scheduleTrucks
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

    /* -------------------------------------------------
     | Relationships
     |------------------------------------------------- */

    /**
     * Lịch trình có thể gắn với nhiều xe tải thông qua bảng pivot schedule_trucks.
     */
    public function trucks()
    {
        return $this->belongsToMany(Truck::class, 'schedule_trucks')
                    ->withPivot(['driver_id', 'from_location_id', 'to_location_id', 'assistant', 'cargo_desc'])
                    ->withTimestamps();
    }

    /**
     * Danh sách các tài xế liên quan đến lịch trình.
     * Thông qua pivot schedule_trucks, sử dụng driver_id.
     */
    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'schedule_trucks')
                    ->withPivot(['truck_id', 'helper_name', 'load_detail'])
                    ->withTimestamps();
    }

    /**
     * Bảng pivot ScheduleTruck (truy cập chi tiết).
     */
    public function scheduleTrucks()
    {
        return $this->hasMany(ScheduleTruck::class);
    }

    /**
     * Nếu có Location cho Schedule (ví dụ: điểm đi/điểm đến).
     */
    public function locations()
    {
        return $this->belongsToMany(Location::class, 'schedule_locations')
                    ->withPivot(['type']) // type: from/to
                    ->withTimestamps();
    }

    /* -------------------------------------------------
     | Query Scopes
     |------------------------------------------------- */

    /**
     * Lấy các lịch trình trong ngày hôm nay.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', now()->toDateString());
    }
}
