<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanetProperty extends Model
{
    use HasFactory, HasUlids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'planet_properties';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'planet_id',
        'type',
        'size',
        'temperature',
        'atmosphere',
        'terrain',
        'resources',
        'description',
    ];

    /**
     * Get the planet that owns these properties.
     */
    public function planet(): BelongsTo
    {
        return $this->belongsTo(Planet::class);
    }
}
