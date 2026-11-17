<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TemporaryPasswordNotification extends Notification
{
    public function __construct(public readonly string $temporaryPassword) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $viewData = [
            'temporaryPassword' => $this->temporaryPassword,
            'loginUrl' => rtrim((string) config('app.url'), '/') . '/login',
        ];

        return (new MailMessage)
            ->subject('Your temporary TrabaGo password')
            ->view('emails.auth.temporary-password', $viewData)
            ->text('emails.auth.temporary-password-text', $viewData);
    }
}
