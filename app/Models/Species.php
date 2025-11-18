<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Breed> $breeds
 * @property-read int|null $breeds_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pet> $pets
 * @property-read int|null $pets_count
 *
 * @method static \Database\Factories\SpeciesFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Species newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Species newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Species query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Species whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Species whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Species whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Species whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Species whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Species whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Species extends Model
{
    /** @use HasFactory<\Database\Factories\SpeciesFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function breeds(): HasMany
    {
        return $this->hasMany(Breed::class);
    }

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }
}
