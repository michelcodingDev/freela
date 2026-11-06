<?php

namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Relates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'type', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Envia a notificação de verificação de e-mail (em português).
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'type' => 'string',
            'status' => 'string',
        ];
    }

    #[Relates]
    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    /** @return HasMany<Project> */
    public function projectsAsClient()
    {
        return $this->hasMany(Project::class, 'client_id');
    }

    /** @return HasMany<Proposal> */
    public function proposalsAsFreelancer()
    {
        return $this->hasMany(Proposal::class, 'freelancer_id');
    }

    /** @return HasMany<Payment> */
    public function paymentsAsClient()
    {
        return $this->hasMany(Payment::class, 'client_id');
    }

    /** @return HasMany<Payment> */
    public function paymentsAsFreelancer()
    {
        return $this->hasMany(Payment::class, 'freelancer_id');
    }

    /** @return HasMany<Review> */
    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'from_user_id');
    }

    /** @return HasMany<Review> */
    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'to_user_id');
    }

    /** @return HasMany<Withdrawal> */
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'freelancer_id');
    }

    /** @return HasMany<Notification> */
    public function appNotifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /** @return HasMany<PortfolioItem> */
    public function portfolioItems()
    {
        return $this->hasMany(PortfolioItem::class, 'freelancer_id');
    }

    /** @return HasMany<UserPlanSubscription> */
    public function subscriptions()
    {
        return $this->hasMany(UserPlanSubscription::class, 'user_id');
    }

    /** @return HasMany<ProjectInvite> */
    public function invitesReceived()
    {
        return $this->hasMany(ProjectInvite::class, 'freelancer_id');
    }

    /** @return BelongsToMany<Skill> */
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skill')->withTimestamps();
    }

    /**
     * Saldo disponível para saque (liberado menos saques ativos).
     */
    public function availableBalance(): float
    {
        $released = $this->paymentsAsFreelancer()->where('status', 'liberado')->sum('net_amount');
        $requested = $this->withdrawals()->whereIn('status', ['solicitado', 'processando'])->sum('net_amount');

        return max(0, (float) $released - (float) $requested);
    }

    /**
     * Valor bloqueado em custódia como freelancer.
     */
    public function lockedBalance(): float
    {
        return (float) $this->paymentsAsFreelancer()->where('status', 'custodia')->sum('net_amount');
    }

    /**
     * Média de avaliações recebidas.
     */
    public function averageRating(): float
    {
        return (float) ($this->reviewsReceived()->avg('rating') ?? 0);
    }

    /**
     * Verifica se o usuário é administrador.
     */
    public function isAdmin(): bool
    {
        return $this->type === 'admin';
    }

    /**
     * Verifica se o usuário é cliente.
     */
    public function isCliente(): bool
    {
        return $this->type === 'cliente';
    }

    /**
     * Verifica se o usuário é freelancer.
     */
    public function isFreelancer(): bool
    {
        return $this->type === 'freelancer';
    }

    /**
     * Verifica se o usuário está ativo.
     */
    public function isActive(): bool
    {
        return $this->status === 'ativo';
    }
}
