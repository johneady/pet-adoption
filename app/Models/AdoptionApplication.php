<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $user_id
 * @property int $pet_id
 * @property string $status
 * @property string $living_situation
 * @property string|null $experience
 * @property string|null $other_pets
 * @property string|null $veterinary_reference
 * @property string|null $household_members
 * @property string|null $employment_status
 * @property string $reason_for_adoption
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Interview|null $interview
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AdoptionApplicationNote> $notes
 * @property-read int|null $notes_count
 * @property-read \App\Models\Pet $pet
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApplicationStatusHistory> $statusHistory
 * @property-read int|null $status_history_count
 * @property-read \App\Models\User $user
 *
 * @method static \Database\Factories\AdoptionApplicationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdoptionApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdoptionApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdoptionApplication query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdoptionApplication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdoptionApplication whereEmploymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdoptionApplication whereExperience($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdoptionApplication whereHouseholdMembers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdoptionApplication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdoptionApplication whereLivingSituation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdoptionApplication whereOtherPets($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdoptionApplication wherePetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdoptionApplication whereReasonForAdoption($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdoptionApplication whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdoptionApplication whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdoptionApplication whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdoptionApplication whereVeterinaryReference($value)
 *
 * @mixin \Eloquent
 */
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
    ];

    protected $attributes = [
        'status' => 'submitted',
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

    public function notes(): HasMany
    {
        return $this->hasMany(AdoptionApplicationNote::class);
    }
}
