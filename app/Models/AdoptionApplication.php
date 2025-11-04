<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AdoptionApplication extends Model
{
    /** @use HasFactory<\Database\Factories\AdoptionApplicationFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pet_id',
        'status',
        'living_situation',
        'experience',
        'other_pets',
        'veterinary_reference',
        'household_members',
        'employment_status',
        'reason_for_adoption',
        'admin_notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(ApplicationStatusHistory::class);
    }

    public function interview(): HasOne
    {
        return $this->hasOne(Interview::class);
    }
}
