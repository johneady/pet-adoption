<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $pet_id
 * @property string $file_path
 * @property bool $is_primary
 * @property int $display_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Pet $pet
 * @method static \Database\Factories\PetPhotoFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PetPhoto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PetPhoto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PetPhoto query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PetPhoto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PetPhoto whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PetPhoto whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PetPhoto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PetPhoto whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PetPhoto wherePetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PetPhoto whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PetPhoto extends Model
{
    /** @use HasFactory<\Database\Factories\PetPhotoFactory> */
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'file_path',
        'is_primary',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}
