<?php

namespace App\Models;

use Database\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Relates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'type', 'title', 'message', 'data', 'is_read', 'read_at'])]
class Notification extends Model
{
    /** @use HasFactory<NotificationFactory> */
    use HasFactory, SoftDeletes;

    #[Relates]
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
