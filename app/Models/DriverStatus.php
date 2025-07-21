<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DriverStatus
 *
 * @property int $id
 * @property string $name
 * @property string|null $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Driver[] $drivers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DriverStatusLog[] $statusLogs
 */
class DriverStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
    ];

    /* -------------------------------------------------
     | Relationships
     |------------------------------------------------- */

    /**
     * Một trạng thái có thể liên kết với nhiều driver thông qua bảng log hoặc cột trạng thái.
     */
    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'driver_status_log', 'status_id', 'driver_id')
                    ->withPivot(['date', 'time_unit'])
                    ->withTimestamps();
    }

    /**
     * Lịch sử thay đổi trạng thái driver.
     */
    public function statusLogs()
    {
        return $this->hasMany(DriverStatusLog::class, 'status_id');
    }
}
