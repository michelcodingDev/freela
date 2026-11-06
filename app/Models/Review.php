<?php

namespace App\Models;

use Database\Factories\ReviewFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Relates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'project_id', 'from_user_id', 'to_user_id', 'rating',
    'comment', 'is_public',
])]
class Review extends Model
{
    /** @use HasFactory<ReviewFactory> */
    use HasFactory, SoftDeletes;

    #[Relates]
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    #[Relates]
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    #[Relates]
    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
