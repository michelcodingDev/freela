<?php

namespace App\Models;

use Database\Factories\UserProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Relates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id', 'phone', 'city', 'state', 'zip_code', 'bio',
    'profile_photo', 'professional_title', 'pix_key',
    'bank_name', 'bank_account_type', 'bank_agency',
    'bank_account', 'bank_account_digit', 'cpf_cnpj',
    'document_photo', 'identity_verified', 'rating_average',
    'total_reviews', 'total_earned', 'projects_completed',
])]
class UserProfile extends Model
{
    /** @use HasFactory<UserProfileFactory> */
    use HasFactory;

    #[Relates]
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
