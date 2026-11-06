<?php

namespace App\Models;

use Database\Factories\PlanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Relates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name', 'slug', 'description', 'price_monthly', 'commission_percentage',
    'min_commission', 'proposals_per_month', 'max_portfolio_items',
    'withdrawal_days', 'is_featured', 'is_active', 'sort_order',
])]
class Plan extends Model
{
    /** @use HasFactory<PlanFactory> */
    use HasFactory, SoftDeletes;

    #[Relates]
    public function features()
    {
        return $this->hasMany(PlanFeature::class);
    }

    #[Relates]
    public function subscriptions()
    {
        return $this->hasMany(UserPlanSubscription::class);
    }

    /**
     * Escopo para planos ativos.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
