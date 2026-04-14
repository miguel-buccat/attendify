<?php

namespace App\Notifications;

use App\Support\SiteSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklyAttendanceSummaryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  array<string, mixed>  $data  Pre-computed summary data for the recipient.
     *                                       Student keys: present, late, absent, excused, total, rate, classes
     *                                       Teacher keys: classes (array of per-class stats)
     */
    public function __construct(
        private readonly string $recipientRole,
        private readonly array $data,
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
        $institutionName = $siteSettings->get('institution_name', 'Attendify');

        $subject = $this->recipientRole === 'student'
            ? "[{$institutionName}] — Your Weekly Attendance Summary"
            : "[{$institutionName}] — Weekly Class Attendance Report";

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.weekly-attendance-summary', [
                'recipientRole' => $this->recipientRole,
                'data' => $this->data,
                'institutionName' => $institutionName,
                'institutionLogo' => $siteSettings->get('institution_logo'),
                'weekLabel' => now()->startOfWeek()->format('M j').' – '.now()->endOfWeek()->format('M j, Y'),
            ]);
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
