<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $id
 * @property int $user_id
 * @property int $pet_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApplicationAnswer> $answers
 * @property-read int|null $answers_count
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

    public function answers(): MorphMany
    {
        return $this->morphMany(ApplicationAnswer::class, 'answerable')->orderBy('sort_order');
    }
}
