<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Relates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'type', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

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
