<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $draw_id
 * @property int $user_id
 * @property int $ticket_number
 * @property bool $is_winner
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Draw $draw
 * @property-read \App\Models\User $user
 *
 * @method static \Database\Factories\DrawTicketFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DrawTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DrawTicket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DrawTicket query()
 *
 * @mixin \Eloquent
 */
class DrawTicket extends Model
{
    /** @use HasFactory<\Database\Factories\DrawTicketFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'draw_id',
        'user_id',
        'ticket_number',
        'is_winner',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_winner' => 'boolean',
        ];
    }

    /**
     * Get the draw this ticket belongs to.
     */
    public function draw(): BelongsTo
    {
        return $this->belongsTo(Draw::class);
    }

    /**
     * Get the user who owns this ticket.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
