<?php

namespace App\Models;

use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Relates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'project_id', 'client_id', 'freelancer_id', 'amount',
    'platform_fee', 'gateway_fee', 'net_amount', 'method',
    'status', 'payment_intent_id', 'gateway_response',
    'paid_at', 'released_at',
])]
class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory, SoftDeletes;

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'gateway_fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    #[Relates]
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    #[Relates]
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    #[Relates]
    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }
}
