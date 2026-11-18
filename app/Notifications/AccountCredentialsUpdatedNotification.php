<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountCredentialsUpdatedNotification extends Notification
{
    public function __construct(
        public readonly string $loginEmail,
        public readonly ?string $updatedPassword
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $viewData = [
            'loginEmail' => $this->loginEmail,
            'updatedPassword' => $this->updatedPassword,
            'loginUrl' => rtrim((string) config('app.url'), '/') . '/login',
        ];

        return (new MailMessage)
            ->subject('Your TrabaGo login details were updated')
            ->view('emails.auth.account-credentials-updated', $viewData)
            ->text('emails.auth.account-credentials-updated-text', $viewData);
    }
}
