<?php

namespace App\Models;

use App\Enums\FormType;
use App\Enums\QuestionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $form_type
 * @property string $label
 * @property string $type
 * @property array|null $options
 * @property bool $is_required
 * @property int $sort_order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Database\Factories\FormQuestionFactory factory($count = null, $state = [])
 * @method static Builder<static>|FormQuestion newModelQuery()
 * @method static Builder<static>|FormQuestion newQuery()
 * @method static Builder<static>|FormQuestion query()
 * @method static Builder<static>|FormQuestion active()
 * @method static Builder<static>|FormQuestion forFormType(FormType $formType)
 *
 * @mixin \Eloquent
 */
class FormQuestion extends Model
{
    /** @use HasFactory<\Database\Factories\FormQuestionFactory> */
    use HasFactory;

    protected $fillable = [
        'form_type',
        'label',
        'type',
        'options',
        'is_required',
        'sort_order',
        'is_active',
    ];

    protected $attributes = [
        'is_required' => true,
        'sort_order' => 0,
        'is_active' => true,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'form_type' => FormType::class,
            'type' => QuestionType::class,
            'options' => 'array',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope a query to only include active questions.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by form type.
     */
    public function scopeForFormType(Builder $query, FormType $formType): Builder
    {
        return $query->where('form_type', $formType);
    }

    /**
     * Get the question data as a snapshot array for storage.
     *
     * @return array<string, mixed>
     */
    public function toSnapshot(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'type' => $this->type->value,
            'options' => $this->options,
            'is_required' => $this->is_required,
        ];
    }
}
