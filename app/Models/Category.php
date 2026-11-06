<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Relates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'slug', 'icon', 'color', 'description', 'sort_order', 'is_active'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory, SoftDeletes;

    #[Relates]
    public function skills()
    {
        return $this->hasMany(Skill::class);
    }

    #[Relates]
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Escopo para categorias ativas.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
