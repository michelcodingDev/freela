<?php

namespace App\Models;

use Database\Factories\ProjectAttachmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectAttachment extends Model
{
    /** @use HasFactory<ProjectAttachmentFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id', 'file_path', 'original_name', 'mime_type', 'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
