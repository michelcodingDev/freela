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

    protected $casts = [
        'proposal_deadline' => 'date',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'featured_fee' => 'decimal:2',
        'is_featured' => 'boolean',
    ];

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
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'project_skill');
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

    #[Relates]
    public function attachments()
    {
        return $this->hasMany(ProjectAttachment::class);
    }

    #[Relates]
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    #[Relates]
    public function disputes()
    {
        return $this->hasMany(Dispute::class);
    }

    #[Relates]
    public function acceptedProposal()
    {
        return $this->hasOne(Proposal::class)->where('status', 'aceita');
    }

    #[Relates]
    public function invites()
    {
        return $this->hasMany(ProjectInvite::class);
    }

    /**
     * Verifica se o usuário pode ver um projeto privado (dono, convidado ou admin).
     */
    public function isVisibleTo(?User $user): bool
    {
        if ($this->visibility !== 'privado') {
            return true;
        }

        if (! $user) {
            return false;
        }

        if ($user->id === $this->client_id || $user->isAdmin()) {
            return true;
        }

        return $this->invites()->where('freelancer_id', $user->id)->exists();
    }

    /**
     * Verifica se o usuário foi convidado.
     */
    public function isInvited(int $userId): bool
    {
        return $this->invites()->where('freelancer_id', $userId)->exists();
    }

    /**
     * Freelancer contratado (proposta aceita).
     */
    public function hiredFreelancer()
    {
        return $this->acceptedProposal?->freelancer;
    }

    /**
     * Verifica se o usuário participa do projeto (cliente ou freelancer contratado).
     */
    public function isParticipant(int $userId): bool
    {
        if ($this->client_id === $userId) {
            return true;
        }

        return $this->acceptedProposal?->freelancer_id === $userId;
    }

    /**
     * Verifica se o projeto pode entrar em execução (pagamento em custódia).
     */
    public function canStartWork(): bool
    {
        return $this->status === 'aguardando_pagamento'
            && $this->payments()->where('status', 'custodia')->exists();
    }

    /**
     * Verifica se o freelancer pode entregar.
     */
    public function canBeDelivered(): bool
    {
        return $this->status === 'em_andamento';
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
