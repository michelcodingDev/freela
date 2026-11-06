<?php

namespace App\Models;

use Database\Factories\WithdrawalFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Relates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'freelancer_id', 'amount', 'fee', 'net_amount',
    'pix_key', 'status', 'paid_at',
])]
class Withdrawal extends Model
{
    /** @use HasFactory<WithdrawalFactory> */
    use HasFactory, SoftDeletes;

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    #[Relates]
    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }
}
