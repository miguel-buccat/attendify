<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Weekly Attendance Summary — {{ $institutionName }}</title>
    </head>
    <body style="margin:0;padding:0;background-color:#f4f4f7;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:24px 12px;background-color:#f4f4f7;">
            <tr>
                <td align="center">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;background-color:#ffffff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">

                        {{-- Header --}}
                        <tr>
                            <td style="padding:24px 24px 12px 24px;background-color:#f9fafb;border-bottom:1px solid #e5e7eb;">
                                @if ($institutionLogo)
                                    <img src="{{ $institutionLogo }}" alt="{{ $institutionName }} logo" style="display:block;max-height:56px;width:auto;margin-bottom:12px;">
                                @endif
                                <h1 style="margin:0;font-size:22px;line-height:1.3;color:#111827;">{{ $institutionName }}</h1>
                                <p style="margin:6px 0 0 0;font-size:14px;line-height:1.4;color:#4b5563;">
                                    Weekly Report &mdash; {{ $weekLabel }}
                                </p>
                            </td>
                        </tr>

                        {{-- Body --}}
                        <tr>
                            <td style="padding:24px;">

                                @if ($recipientRole === 'student')
                                    {{-- Student summary --}}
                                    <p style="margin:0 0 14px 0;font-size:15px;line-height:1.6;">Hello,</p>
                                    <p style="margin:0 0 20px 0;font-size:15px;line-height:1.6;">
                                        Here is your personal attendance summary for the week of <strong>{{ $weekLabel }}</strong>.
                                    </p>

                                    {{-- Overall stats box --}}
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 20px 0;background-color:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;overflow:hidden;">
                                        <tr>
                                            <td style="padding:16px 20px;">
                                                <p style="margin:0 0 12px 0;font-size:13px;font-weight:700;color:#15803d;text-transform:uppercase;letter-spacing:.06em;">Your Week at a Glance</p>
                                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td style="padding:4px 8px 4px 0;font-size:13px;font-weight:600;color:#166534;width:25%;text-align:center;">Present</td>
                                                        <td style="padding:4px 8px 4px 0;font-size:13px;font-weight:600;color:#92400e;width:25%;text-align:center;">Late</td>
                                                        <td style="padding:4px 8px 4px 0;font-size:13px;font-weight:600;color:#991b1b;width:25%;text-align:center;">Absent</td>
                                                        <td style="padding:4px 0;font-size:13px;font-weight:600;color:#1e40af;width:25%;text-align:center;">Excused</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:2px 8px 8px 0;font-size:22px;font-weight:900;color:#166534;text-align:center;">{{ $data['present'] }}</td>
                                                        <td style="padding:2px 8px 8px 0;font-size:22px;font-weight:900;color:#92400e;text-align:center;">{{ $data['late'] }}</td>
                                                        <td style="padding:2px 8px 8px 0;font-size:22px;font-weight:900;color:#991b1b;text-align:center;">{{ $data['absent'] }}</td>
                                                        <td style="padding:2px 0 8px 0;font-size:22px;font-weight:900;color:#1e40af;text-align:center;">{{ $data['excused'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" style="padding:8px 0 0 0;border-top:1px solid #bbf7d0;font-size:13px;color:#374151;">
                                                            Weekly attendance rate: <strong>{{ $data['rate'] }}%</strong>
                                                            &nbsp;&bull;&nbsp;
                                                            {{ $data['total'] }} session{{ $data['total'] !== 1 ? 's' : '' }} this week
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>

                                    @if (! empty($data['classes']))
                                        <p style="margin:0 0 10px 0;font-size:14px;font-weight:700;color:#111827;">Per-Class Breakdown</p>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;margin-bottom:20px;">
                                            <tr style="background-color:#f9fafb;">
                                                <td style="padding:8px 14px;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Class</td>
                                                <td style="padding:8px 14px;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;text-align:center;">Sessions</td>
                                                <td style="padding:8px 14px;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;text-align:center;">Status</td>
                                            </tr>
                                            @foreach ($data['classes'] as $cls)
                                                <tr style="border-top:1px solid #f3f4f6;">
                                                    <td style="padding:10px 14px;font-size:13px;font-weight:600;color:#111827;">{{ $cls['name'] }}</td>
                                                    <td style="padding:10px 14px;font-size:13px;color:#374151;text-align:center;">{{ $cls['sessions'] }}</td>
                                                    <td style="padding:10px 14px;font-size:13px;color:#374151;text-align:center;">{{ $cls['status'] }}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    @endif

                                @else
                                    {{-- Teacher summary --}}
                                    <p style="margin:0 0 14px 0;font-size:15px;line-height:1.6;">Hello,</p>
                                    <p style="margin:0 0 20px 0;font-size:15px;line-height:1.6;">
                                        Here is the attendance report for your classes for the week of <strong>{{ $weekLabel }}</strong>.
                                    </p>

                                    @if (empty($data['classes']))
                                        <p style="margin:0 0 20px 0;font-size:14px;line-height:1.6;color:#6b7280;">
                                            No sessions were held this week.
                                        </p>
                                    @else
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;margin-bottom:20px;">
                                            <tr style="background-color:#f9fafb;">
                                                <td style="padding:8px 14px;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Class</td>
                                                <td style="padding:8px 14px;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;text-align:center;">Sessions</td>
                                                <td style="padding:8px 14px;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;text-align:center;">Avg. Rate</td>
                                                <td style="padding:8px 14px;font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;text-align:center;">Absences</td>
                                            </tr>
                                            @foreach ($data['classes'] as $cls)
                                                <tr style="border-top:1px solid #f3f4f6;">
                                                    <td style="padding:10px 14px;font-size:13px;font-weight:600;color:#111827;">{{ $cls['name'] }}</td>
                                                    <td style="padding:10px 14px;font-size:13px;color:#374151;text-align:center;">{{ $cls['sessions'] }}</td>
                                                    <td style="padding:10px 14px;font-size:13px;color:#374151;text-align:center;">{{ $cls['rate'] }}%</td>
                                                    <td style="padding:10px 14px;font-size:13px;color:#991b1b;font-weight:600;text-align:center;">{{ $cls['absences'] }}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    @endif
                                @endif

                                <p style="margin:0;font-size:13px;line-height:1.6;color:#6b7280;">
                                    This report covers the period {{ $weekLabel }}. For detailed records, log in to the Attendify system.
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
