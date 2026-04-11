<?php

namespace App\Notifications\Auth;

use App\Support\SiteSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $resetUrl,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        /** @var SiteSettings $siteSettings */
        $siteSettings = app(SiteSettings::class);

        return (new MailMessage)
            ->subject('Reset your password')
            ->view('emails.auth.reset-password', [
                'userName' => $notifiable->name,
                'resetUrl' => $this->resetUrl,
                'institutionName' => $siteSettings->get('institution_name', config('app.name', 'Attendify')),
                'institutionLogo' => $siteSettings->get('institution_logo'),
            ]);
    }
}
