<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Absence Notice: {{ $studentName }}</title>
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
                                <p style="margin:6px 0 0 0;font-size:14px;line-height:1.4;color:#4b5563;">Attendance Notification</p>
                            </td>
                        </tr>

                        <tr>
                            <td style="padding:24px;">
                                <p style="margin:0 0 14px 0;font-size:15px;line-height:1.6;">Dear Parent / Guardian,</p>
                                <p style="margin:0 0 18px 0;font-size:15px;line-height:1.6;">
                                    This is to inform you that <strong>{{ $studentName }}</strong> was marked
                                    <strong style="color:#dc2626;">absent</strong> from the following class session:
                                </p>

                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fef2f2;border:1px solid #fecaca;border-radius:8px;margin-bottom:20px;">
                                    <tr>
                                        <td style="padding:16px 20px;">
                                            <table role="presentation" width="100%">
                                                <tr>
                                                    <td style="padding:4px 0;font-size:13px;color:#6b7280;width:120px;">Class</td>
                                                    <td style="padding:4px 0;font-size:14px;font-weight:600;color:#111827;">{{ $className }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:4px 0;font-size:13px;color:#6b7280;">Date</td>
                                                    <td style="padding:4px 0;font-size:14px;color:#111827;">{{ $sessionDate }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:4px 0;font-size:13px;color:#6b7280;">Time</td>
                                                    <td style="padding:4px 0;font-size:14px;color:#111827;">{{ $sessionTime }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:4px 0;font-size:13px;color:#6b7280;">Modality</td>
                                                    <td style="padding:4px 0;font-size:14px;color:#111827;">{{ $modality }}</td>
                                                </tr>
                                                @if ($location)
                                                <tr>
                                                    <td style="padding:4px 0;font-size:13px;color:#6b7280;">Location</td>
                                                    <td style="padding:4px 0;font-size:14px;color:#111827;">{{ $location }}</td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td style="padding:4px 0;font-size:13px;color:#6b7280;">Recorded at</td>
                                                    <td style="padding:4px 0;font-size:14px;color:#111827;">{{ $markedAt }}</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>

                                <p style="margin:0 0 10px 0;font-size:14px;line-height:1.6;color:#4b5563;">
                                    If you believe this is an error, please contact your child's teacher or school administrator.
                                </p>
                                <p style="margin:0;font-size:14px;line-height:1.6;color:#4b5563;">
                                    Your child may also submit an excuse request through the Attendify system if applicable.
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <td style="padding:16px 24px;background-color:#f9fafb;border-top:1px solid #e5e7eb;">
                                <p style="margin:0;font-size:12px;line-height:1.6;color:#6b7280;">This is a system-generated email from the Attendify system at {{ $institutionName }}. Please do not reply to this message.</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
