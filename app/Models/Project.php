<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Relates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'client_id', 'category_id', 'title', 'slug', 'description',
    'experience_level', 'visibility', 'status', 'proposal_deadline',
    'budget_min', 'budget_max', 'is_featured', 'featured_fee',
    'attachment_path', 'admin_notes', 'approved_at', 'completed_at',
])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, SoftDeletes;

    #[Relates]
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    #[Relates]
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    #[Relates]
    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    #[Relates]
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    #[Relates]
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Verifica se o projeto pode receber propostas.
     */
    public function canReceiveProposals(): bool
    {
        return $this->status === 'aguardando_propostas';
    }

    /**
     * Verifica se o projeto pode ser pago.
     */
    public function canAcceptPayment(): bool
    {
        return $this->status === 'aguardando_pagamento';
    }
}
