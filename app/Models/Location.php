<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string|null $address
 * @property string|null $link
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'link',
    ];

    /* ---------------- Relationships ---------------- */

    public function fromScheduleTrucks()
    {
        return $this->hasMany(ScheduleTruck::class, 'from_location_id');
    }

    public function toScheduleTrucks()
    {
        return $this->hasMany(ScheduleTruck::class, 'to_location_id');
    }
}
