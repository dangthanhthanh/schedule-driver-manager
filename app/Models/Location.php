<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string|null $address
 * @property string|null $link
 */
class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'link',
    ];

    // Relationships

    public function fromScheduleTrucks(): HasMany
    {
        return $this->hasMany(ScheduleTruck::class, 'from_location_id');
    }

    public function toScheduleTrucks(): HasMany
    {
        return $this->hasMany(ScheduleTruck::class, 'to_location_id');
    }
}