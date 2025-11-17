<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property float $annual_price
 * @property float $monthly_price
 * @property string|null $stripe_annual_price_id
 * @property string|null $stripe_monthly_price_id
 * @property string|null $description
 * @property array|null $features
 * @property string $badge_color
 * @property string $badge_icon
 * @property int $display_order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class MembershipPlan extends Model
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
            'annual_price' => 'decimal:2',
            'monthly_price' => 'decimal:2',
            'features' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the memberships for this plan.
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class, 'plan_id');
    }

    /**
     * Scope a query to only include active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * Get the formatted annual price.
     */
    public function getFormattedAnnualPriceAttribute(): string
    {
        return '$'.number_format($this->annual_price, 2);
    }

    /**
     * Get the formatted monthly price.
     */
    public function getFormattedMonthlyPriceAttribute(): string
    {
        return '$'.number_format($this->monthly_price, 2);
    }
}
