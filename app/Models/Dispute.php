<?php

namespace App\Models;

use Database\Factories\DisputeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Relates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'project_id', 'client_id', 'freelancer_id', 'reason',
    'client_argument', 'freelancer_argument', 'status',
    'winner', 'resolution_notes', 'resolved_by', 'resolved_at',
    'partial_percentage',
])]
class Dispute extends Model
{
    /** @use HasFactory<DisputeFactory> */
    use HasFactory, SoftDeletes;

    protected $casts = [
        'resolved_at' => 'datetime',
        'partial_percentage' => 'decimal:2',
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

    #[Relates]
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
