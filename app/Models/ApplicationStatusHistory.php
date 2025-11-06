<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $adoption_application_id
 * @property string|null $from_status
 * @property string $to_status
 * @property string|null $notes
 * @property int|null $changed_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AdoptionApplication $adoptionApplication
 * @property-read \App\Models\User|null $changedBy
 * @method static \Database\Factories\ApplicationStatusHistoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStatusHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStatusHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStatusHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStatusHistory whereAdoptionApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStatusHistory whereChangedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStatusHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStatusHistory whereFromStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStatusHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStatusHistory whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStatusHistory whereToStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationStatusHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ApplicationStatusHistory extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationStatusHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'adoption_application_id',
        'from_status',
        'to_status',
        'notes',
        'changed_by',
    ];

    public function adoptionApplication(): BelongsTo
    {
        return $this->belongsTo(AdoptionApplication::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
