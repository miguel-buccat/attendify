<?php

namespace App\Notifications\Auth;

use App\Models\Invitation;
use App\Support\SiteSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Invitation $invitation,
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

        $acceptUrl = url(route('invitation.accept', ['token' => $this->invitation->token], false));

        return (new MailMessage)
            ->subject('You have been invited to join '.$siteSettings->get('institution_name', 'Attendify'))
            ->view('emails.invitation', [
                'invitation' => $this->invitation,
                'acceptUrl' => $acceptUrl,
                'institutionName' => $siteSettings->get('institution_name', 'Attendify'),
                'institutionLogo' => $siteSettings->get('institution_logo'),
            ]);
    }
}
