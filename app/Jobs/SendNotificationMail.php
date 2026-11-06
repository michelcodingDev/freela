<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNotificationMail implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public string $title,
        public string $message,
    ) {}

    public function handle(): void
    {
        try {
            Mail::raw(
                $this->title."\n\n".$this->message,
                function ($mail) {
                    $mail->to($this->user->email)->subject('[FreelaJob] '.$this->title);
                }
            );
        } catch (\Throwable $e) {
            Log::warning('Falha ao enviar e-mail de notificação', ['user' => $this->user->id, 'error' => $e->getMessage()]);
        }
    }
}
