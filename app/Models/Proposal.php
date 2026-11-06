<?php

namespace App\Models;

use Database\Factories\ProposalFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Relates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'project_id', 'freelancer_id', 'offer', 'final_offer',
    'estimated_days', 'details', 'status', 'is_promoted',
    'promoted_fee',
])]
class Proposal extends Model
{
    /** @use HasFactory<ProposalFactory> */
    use HasFactory, SoftDeletes;

    #[Relates]
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    #[Relates]
    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }
}
