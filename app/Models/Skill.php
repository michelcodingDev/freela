<?php

namespace App\Models;

use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Relates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'slug', 'category_id'])]
class Skill extends Model
{
    /** @use HasFactory<SkillFactory> */
    use HasFactory, SoftDeletes;

    #[Relates]
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    #[Relates]
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_skill')->withTimestamps();
    }

    #[Relates]
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_skill');
    }
}
