<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TruckStatus
 *
 * @property int $id
 * @property string $name
 * @property string|null $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Truck[] $trucks
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TruckStatusLog[] $statusLogs
 */
class TruckStatus extends Model
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
     * Trạng thái được áp dụng cho nhiều xe tải (quan hệ nhiều-nhiều qua logs hoặc cột hiện tại).
     */
    public function trucks()
    {
        return $this->belongsToMany(Truck::class, 'truck_status_log', 'status_id', 'truck_id')
                    ->withPivot(['date', 'time_unit'])
                    ->withTimestamps();
    }

    /**
     * Lịch sử thay đổi trạng thái xe tải.
     */
    public function statusLogs()
    {
        return $this->hasMany(TruckStatusLog::class, 'status_id');
    }
}
