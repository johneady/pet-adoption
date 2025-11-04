<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Breed extends Model
{
    /** @use HasFactory<\Database\Factories\BreedFactory> */
    use HasFactory;

    protected $fillable = [
        'species_id',
        'name',
        'slug',
        'description',
    ];

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }
}
