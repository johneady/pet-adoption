<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property array $ticket_price_tiers
 * @property \Illuminate\Support\Carbon $starts_at
 * @property \Illuminate\Support\Carbon $ends_at
 * @property bool $is_finalized
 * @property int|null $winner_ticket_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DrawTicket> $tickets
 * @property-read int|null $tickets_count
 * @property-read \App\Models\DrawTicket|null $winnerTicket
 *
 * @method static \Database\Factories\DrawFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Draw newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Draw newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Draw query()
 *
 * @mixin \Eloquent
 */
class Draw extends Model
{
    /** @use HasFactory<\Database\Factories\DrawFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'ticket_price_tiers',
        'starts_at',
        'ends_at',
        'is_finalized',
        'winner_ticket_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ticket_price_tiers' => 'array',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_finalized' => 'boolean',
        ];
    }

    /**
     * Get the tickets for this draw.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(DrawTicket::class);
    }

    /**
     * Get the winning ticket.
     */
    public function winnerTicket(): BelongsTo
    {
        return $this->belongsTo(DrawTicket::class, 'winner_ticket_id');
    }

    /**
     * Check if the draw is currently active.
     */
    public function isActive(): bool
    {
        $now = Carbon::now();

        return $now->greaterThanOrEqualTo($this->starts_at)
            && $now->lessThan($this->ends_at)
            && ! $this->is_finalized;
    }

    /**
     * Check if the draw has ended.
     */
    public function hasEnded(): bool
    {
        return Carbon::now()->greaterThanOrEqualTo($this->ends_at);
    }

    /**
     * Check if the draw has started.
     */
    public function hasStarted(): bool
    {
        return Carbon::now()->greaterThanOrEqualTo($this->starts_at);
    }

    /**
     * Get the total number of tickets sold.
     */
    public function totalTicketsSold(): int
    {
        return $this->tickets()->count();
    }

    /**
     * Get the total amount collected from ticket sales.
     */
    public function totalAmountCollected(): float
    {
        $ticketCount = $this->totalTicketsSold();
        $tiers = collect($this->ticket_price_tiers)->sortByDesc('quantity');

        $total = 0.0;
        $remaining = $ticketCount;

        // Calculate based on best value per ticket
        foreach ($tiers as $tier) {
            $pricePerTicket = $tier['price'] / $tier['quantity'];
            $total += $remaining * $pricePerTicket;
            break; // Use the first tier's price per ticket as estimate
        }

        return $total;
    }

    /**
     * Get the prize amount (50% of total collected).
     */
    public function prizeAmount(): float
    {
        return $this->totalAmountCollected() / 2;
    }

    /**
     * Get the duration of the draw in days.
     */
    public function durationInDays(): int
    {
        return $this->starts_at->diffInDays($this->ends_at);
    }

    /**
     * Get the next available ticket number.
     */
    public function nextTicketNumber(): int
    {
        return ($this->tickets()->max('ticket_number') ?? 0) + 1;
    }

    /**
     * Select a random winner from the tickets.
     */
    public function selectRandomWinner(): ?DrawTicket
    {
        if ($this->is_finalized) {
            return null;
        }

        $winningTicket = $this->tickets()->inRandomOrder()->first();

        if ($winningTicket) {
            $winningTicket->update(['is_winner' => true]);
            $this->update([
                'winner_ticket_id' => $winningTicket->id,
                'is_finalized' => true,
            ]);
        }

        return $winningTicket;
    }

    /**
     * Get tickets grouped by user.
     *
     * @return \Illuminate\Support\Collection
     */
    public function ticketsByUser()
    {
        return $this->tickets()
            ->with('user')
            ->get()
            ->groupBy('user_id')
            ->map(function ($tickets) {
                return [
                    'user' => $tickets->first()->user,
                    'count' => $tickets->count(),
                    'tickets' => $tickets,
                ];
            });
    }
}
