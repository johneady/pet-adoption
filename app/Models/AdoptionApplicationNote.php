<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $adoption_application_id
 * @property string $note
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AdoptionApplication $adoptionApplication
 * @property-read \App\Models\User|null $createdBy
 *
 * @method static \Database\Factories\AdoptionApplicationNoteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdoptionApplicationNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdoptionApplicationNote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdoptionApplicationNote query()
 *
 * @mixin \Eloquent
 */
class AdoptionApplicationNote extends Model
{
    /** @use HasFactory<\Database\Factories\AdoptionApplicationNoteFactory> */
    use HasFactory;

    protected $fillable = [
        'adoption_application_id',
        'note',
        'created_by',
    ];

    public function adoptionApplication(): BelongsTo
    {
        return $this->belongsTo(AdoptionApplication::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
