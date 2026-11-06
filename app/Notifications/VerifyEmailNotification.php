<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Confirme seu e-mail na FreelaJob')
            ->greeting('Olá, '.$notifiable->name.'!')
            ->line('Obrigado por se cadastrar na FreelaJob. Para confirmar seu e-mail e liberar todos os recursos da conta, clique no botão abaixo.')
            ->action('Confirmar e-mail', $url)
            ->line('Se você não criou esta conta, pode ignorar este e-mail com segurança.')
            ->line('Este link expira em 60 minutos. Se expirar, solicite um novo link na tela de verificação.');
    }

    /**
     * Monta a URL assinada de verificação para o usuário.
     */
    protected function verificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );
    }
}
