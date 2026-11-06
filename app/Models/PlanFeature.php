<?php

namespace App\Models;

use Database\Factories\PlanFeatureFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Relates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['plan_id', 'name', 'description', 'is_included'])]
class PlanFeature extends Model
{
    /** @use HasFactory<PlanFeatureFactory> */
    use HasFactory, SoftDeletes;

    #[Relates]
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
