<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StarSystem extends Model
{
    use HasFactory, HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'x',
        'y',
        'z',
        'star_type',
        'planet_count',
        'discovered',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'x' => 'decimal:2',
        'y' => 'decimal:2',
        'z' => 'decimal:2',
        'discovered' => 'boolean',
        'planet_count' => 'integer',
    ];

    /**
     * Get all planets in this star system.
     */
    public function planets(): HasMany
    {
        return $this->hasMany(Planet::class);
    }

    /**
     * Calculate distance to another star system.
     */
    public function distanceTo(StarSystem $other): float
    {
        return sqrt(
            pow($this->x - $other->x, 2) +
            pow($this->y - $other->y, 2) +
            pow($this->z - $other->z, 2)
        );
    }

    /**
     * Find nearby star systems within a given radius.
     */
    public static function nearby(float $x, float $y, float $z, float $radius): \Illuminate\Database\Eloquent\Collection
    {
        return self::whereBetween('x', [$x - $radius, $x + $radius])
            ->whereBetween('y', [$y - $radius, $y + $radius])
            ->whereBetween('z', [$z - $radius, $z + $radius])
            ->get()
            ->filter(function ($system) use ($x, $y, $z, $radius) {
                $distance = sqrt(
                    pow($system->x - $x, 2) +
                    pow($system->y - $y, 2) +
                    pow($system->z - $z, 2)
                );

                return $distance <= $radius;
            });
    }
}
