<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property int $plan_id
 * @property string $payment_type
 * @property string $status
 * @property float $amount_paid
 * @property string|null $stripe_subscription_id
 * @property string|null $stripe_payment_intent_id
 * @property \Illuminate\Support\Carbon $started_at
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $canceled_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Membership extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount_paid' => 'decimal:2',
            'started_at' => 'datetime',
            'expires_at' => 'datetime',
            'canceled_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the membership.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the plan that this membership belongs to.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class, 'plan_id');
    }

    /**
     * Get the transactions for this membership.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(MembershipTransaction::class);
    }

    /**
     * Scope a query to only include active memberships.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '>', now());
    }

    /**
     * Scope a query to only include expired memberships.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now())
            ->where('status', 'active');
    }

    /**
     * Check if the membership is currently active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at->isFuture();
    }

    /**
     * Get the number of days remaining in the membership.
     */
    public function daysRemaining(): int
    {
        if (! $this->isActive()) {
            return 0;
        }

        return (int) now()->diffInDays($this->expires_at, false);
    }

    /**
     * Cancel the membership.
     */
    public function cancel(): void
    {
        $this->update([
            'status' => 'canceled',
            'canceled_at' => now(),
        ]);
    }

    /**
     * Refund the membership.
     */
    public function refund(): void
    {
        $this->update([
            'status' => 'refunded',
        ]);
    }
}
