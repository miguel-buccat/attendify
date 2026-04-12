<?php

namespace App\Notifications;

use App\Models\ClassSession;
use App\Support\SiteSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClassSessionStartedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly ClassSession $session,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        /** @var SiteSettings $siteSettings */
        $siteSettings = app(SiteSettings::class);

        $class = $this->session->schoolClass;
        $institutionName = $siteSettings->get('institution_name', 'Attendify');

        return (new MailMessage)
            ->subject("[{$institutionName}] — Class session started: {$class->name}")
            ->view('emails.class-session-started', [
                'session' => $this->session,
                'class' => $class,
                'institutionName' => $institutionName,
                'institutionLogo' => $siteSettings->get('institution_logo'),
                'sessionDate' => $this->session->start_time->format('F j, Y'),
                'sessionTime' => $this->session->start_time->format('g:i A').' – '.$this->session->end_time->format('g:i A'),
            ]);
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
