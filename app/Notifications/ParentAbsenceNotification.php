<?php

namespace App\Notifications;

use App\Models\AttendanceRecord;
use App\Support\SiteSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ParentAbsenceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly AttendanceRecord $record,
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

        $student = $this->record->student;
        $session = $this->record->classSession->load('schoolClass');
        $class = $session->schoolClass;
        $institutionName = $siteSettings->get('institution_name', 'Attendify');

        $sessionDate = $session->start_time->format('F j, Y');
        $sessionTime = $session->start_time->format('g:i A').' – '.$session->end_time->format('g:i A');

        return (new MailMessage)
            ->subject("Absence Notice: {$student->name} — {$class->name}")
            ->view('emails.parent-absence', [
                'studentName' => $student->name,
                'className' => $class->name,
                'sessionDate' => $sessionDate,
                'sessionTime' => $sessionTime,
                'modality' => $session->modality->value,
                'location' => $session->location,
                'markedAt' => now()->format('g:i A, F j'),
                'institutionName' => $institutionName,
                'institutionLogo' => $siteSettings->get('institution_logo'),
            ]);
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
