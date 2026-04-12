<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Class Session Started — {{ $class->name }}</title>
    </head>
    <body style="margin:0;padding:0;background-color:#f4f4f7;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:24px 12px;background-color:#f4f4f7;">
            <tr>
                <td align="center">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;background-color:#ffffff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
                        <tr>
                            <td style="padding:24px 24px 12px 24px;background-color:#f9fafb;border-bottom:1px solid #e5e7eb;">
                                @if ($institutionLogo)
                                    <img src="{{ $institutionLogo }}" alt="{{ $institutionName }} logo" style="display:block;max-height:56px;width:auto;margin-bottom:12px;">
                                @endif
                                <h1 style="margin:0;font-size:22px;line-height:1.3;color:#111827;">{{ $institutionName }}</h1>
                                <p style="margin:6px 0 0 0;font-size:14px;line-height:1.4;color:#4b5563;">Attendance Reminder</p>
                            </td>
                        </tr>

                        <tr>
                            <td style="padding:24px;">
                                <p style="margin:0 0 14px 0;font-size:15px;line-height:1.6;">Hello,</p>
                                <p style="margin:0 0 18px 0;font-size:15px;line-height:1.6;">
                                    Your teacher has just started a session for <strong>{{ $class->name }}</strong>.
                                    Please make your way to class and scan the QR code displayed by your teacher to mark your attendance.
                                </p>

                                {{-- Session info box --}}
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px 0;background-color:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;overflow:hidden;">
                                    <tr>
                                        <td style="padding:16px 20px;">
                                            <p style="margin:0 0 10px 0;font-size:13px;font-weight:700;color:#0369a1;text-transform:uppercase;letter-spacing:.06em;">Session Details</p>
                                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="padding:4px 0;font-size:13px;color:#64748b;width:40%;">Class</td>
                                                    <td style="padding:4px 0;font-size:13px;font-weight:600;color:#1e293b;">{{ $class->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:4px 0;font-size:13px;color:#64748b;">Date</td>
                                                    <td style="padding:4px 0;font-size:13px;font-weight:600;color:#1e293b;">{{ $sessionDate }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:4px 0;font-size:13px;color:#64748b;">Time</td>
                                                    <td style="padding:4px 0;font-size:13px;font-weight:600;color:#1e293b;">{{ $sessionTime }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:4px 0;font-size:13px;color:#64748b;">Modality</td>
                                                    <td style="padding:4px 0;font-size:13px;font-weight:600;color:#1e293b;">{{ $session->modality->value }}</td>
                                                </tr>
                                                @if ($session->location)
                                                    <tr>
                                                        <td style="padding:4px 0;font-size:13px;color:#64748b;">Location</td>
                                                        <td style="padding:4px 0;font-size:13px;font-weight:600;color:#1e293b;">{{ $session->location }}</td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td style="padding:4px 0;font-size:13px;color:#64748b;">Grace Period</td>
                                                    <td style="padding:4px 0;font-size:13px;font-weight:600;color:#1e293b;">{{ $session->grace_period_minutes }} minutes</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>

                                <p style="margin:0 0 10px 0;font-size:14px;line-height:1.6;color:#4b5563;">
                                    Arriving within the grace period will mark you as <strong>Present</strong>. After that, you will be marked <strong>Late</strong>.
                                    If you cannot attend, please contact your teacher directly.
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <td style="padding:16px 24px;background-color:#f9fafb;border-top:1px solid #e5e7eb;">
                                <p style="margin:0;font-size:12px;line-height:1.6;color:#6b7280;">This is a system-generated email from the Attendify system. Please do not reply to this message.</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
