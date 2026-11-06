<?php

namespace App\Http\Controllers;

use App\Models\ProjectAttachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    /**
     * Download seguro de anexo de projeto (somente quem pode ver o projeto).
     */
    public function download(ProjectAttachment $attachment)
    {
        $user = Auth::user();
        $project = $attachment->project;

        $canAccess = $project->isVisibleTo($user) || $project->isParticipant($user->id);

        if (! $canAccess && ! $user->isAdmin()) {
            abort(403, 'Você não tem permissão para acessar este anexo.');
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($attachment->file_path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return $disk->download($attachment->file_path, $attachment->original_name);
    }
}
