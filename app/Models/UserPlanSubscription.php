<?php

namespace App\Models;

use Database\Factories\UserPlanSubscriptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Relates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id', 'plan_id', 'status', 'starts_at', 'ends_at',
    'price_paid', 'payment_method', 'payment_id',
])]
class UserPlanSubscription extends Model
{
    /** @use HasFactory<UserPlanSubscriptionFactory> */
    use HasFactory, SoftDeletes;

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'price_paid' => 'decimal:2',
    ];

    #[Relates]
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    #[Relates]
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Verifica se a assinatura está ativa.
     */
    public function isActive(): bool
    {
        return $this->status === 'ativo' && $this->ends_at > now();
    }
}
