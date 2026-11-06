<?php

namespace App\Models;

use Database\Factories\PortfolioItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Relates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['freelancer_id', 'title', 'description', 'file_path', 'external_url', 'is_public'])]
class PortfolioItem extends Model
{
    /** @use HasFactory<PortfolioItemFactory> */
    use HasFactory, SoftDeletes;

    #[Relates]
    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }
}
