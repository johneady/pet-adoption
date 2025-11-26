<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $species_id
 * @property int|null $breed_id
 * @property string $name
 * @property string $slug
 * @property int|null $age
 * @property string $gender
 * @property string|null $size
 * @property string|null $color
 * @property string|null $description
 * @property string|null $medical_notes
 * @property bool $vaccination_status
 * @property bool $special_needs
 * @property \Illuminate\Support\Carbon $intake_date
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AdoptionApplication> $adoptionApplications
 * @property-read int|null $adoption_applications_count
 * @property-read \App\Models\Breed|null $breed
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PetPhoto> $photos
 * @property-read int|null $photos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PetPhoto> $primaryPhoto
 * @property-read int|null $primary_photo_count
 * @property-read \App\Models\Species $species
 *
 * @method static \Database\Factories\PetFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pet query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pet whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pet whereBreedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pet whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pet whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pet whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pet whereIntakeDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pet whereMedicalNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pet whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pet whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pet whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pet whereSpeciesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pet whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pet whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Pet extends Model
{
    /** @use HasFactory<\Database\Factories\PetFactory> */
    use HasFactory, SoftDeletes;

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
        'vaccination_status',
        'special_needs',
        'intake_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'intake_date' => 'date',
            'vaccination_status' => 'boolean',
            'special_needs' => 'boolean',
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

    public function primaryPhoto(): HasOne
    {
        return $this->hasOne(PetPhoto::class)->where('is_primary', true);
    }

    public function adoptionApplications(): HasMany
    {
        return $this->hasMany(AdoptionApplication::class);
    }
}
