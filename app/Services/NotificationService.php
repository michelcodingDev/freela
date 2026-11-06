<?php

namespace App\Services;

use App\Jobs\SendNotificationMail;
use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Cria uma notificação in-app para o usuário (e e-mail quando configurado).
     *
     * @param  array<string, mixed>|null  $data
     */
    public function notify(?User $user, string $type, string $title, string $message, ?array $data = null): ?Notification
    {
        if (! $user) {
            return null;
        }

        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_read' => false,
        ]);

        if (config('mail.default') !== 'log' && config('mail.default') !== null) {
            SendNotificationMail::dispatch($user, $title, $message);
        }

        return $notification;
    }

    public function unreadCount(User $user): int
    {
        return $user->appNotifications()->where('is_read', false)->count();
    }
}
