<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $answerable_type
 * @property int $answerable_id
 * @property array $question_snapshot
 * @property string|null $answer
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $answerable
 *
 * @method static \Database\Factories\ApplicationAnswerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationAnswer query()
 *
 * @mixin \Eloquent
 */
class ApplicationAnswer extends Model
{
    /** @use HasFactory<\Database\Factories\ApplicationAnswerFactory> */
    use HasFactory;

    protected $fillable = [
        'answerable_type',
        'answerable_id',
        'question_snapshot',
        'answer',
        'sort_order',
    ];

    protected $attributes = [
        'sort_order' => 0,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'question_snapshot' => 'array',
        ];
    }

    /**
     * Get the parent answerable model (AdoptionApplication, FosteringApplication, etc.).
     */
    public function answerable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the question label from the snapshot.
     */
    public function getQuestionLabelAttribute(): string
    {
        return $this->question_snapshot['label'] ?? '';
    }

    /**
     * Get the question type from the snapshot.
     */
    public function getQuestionTypeAttribute(): string
    {
        return $this->question_snapshot['type'] ?? 'string';
    }

    /**
     * Get the formatted answer for display.
     */
    public function getFormattedAnswerAttribute(): string
    {
        $type = $this->question_type;
        $answer = $this->answer;

        if ($answer === null || $answer === '') {
            return 'Not provided';
        }

        if ($type === 'switch') {
            return $answer === '1' || $answer === 'true' || $answer === true ? 'Yes' : 'No';
        }

        return (string) $answer;
    }
}
