<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketPurchaseRequest extends Model
{
    /** @use HasFactory<\Database\Factories\TicketPurchaseRequestFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'draw_id',
        'user_id',
        'quantity',
        'pricing_tier',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'pricing_tier' => 'array',
        ];
    }

    /**
     * Get the draw for this purchase request.
     */
    public function draw(): BelongsTo
    {
        return $this->belongsTo(Draw::class);
    }

    /**
     * Get the user who made this purchase request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include fulfilled requests.
     */
    public function scopeFulfilled($query)
    {
        return $query->where('status', 'fulfilled');
    }
}
