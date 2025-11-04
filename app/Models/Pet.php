<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pet extends Model
{
    /** @use HasFactory<\Database\Factories\PetFactory> */
    use HasFactory;

    protected $fillable = [
        'species_id',
        'breed_id',
        'name',
        'slug',
        'age',
        'gender',
        'size',
        'color',
        'description',
        'medical_notes',
        'intake_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'intake_date' => 'date',
        ];
    }

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(Breed::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PetPhoto::class);
    }

    public function primaryPhoto(): HasMany
    {
        return $this->hasMany(PetPhoto::class)->where('is_primary', true)->limit(1);
    }

    public function adoptionApplications(): HasMany
    {
        return $this->hasMany(AdoptionApplication::class);
    }
}
