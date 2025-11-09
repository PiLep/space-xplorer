<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Planet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'size',
        'temperature',
        'atmosphere',
        'terrain',
        'resources',
        'description',
    ];

    /**
     * Get the users that have this planet as their home planet.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'home_planet_id');
    }
}
