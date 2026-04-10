<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Reset your password</title>
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
                                <p style="margin:6px 0 0 0;font-size:14px;line-height:1.4;color:#4b5563;">Password Reset Request</p>
                            </td>
                        </tr>

                        <tr>
                            <td style="padding:24px;">
                                <p style="margin:0 0 14px 0;font-size:15px;line-height:1.6;">Hello {{ $userName }},</p>
                                <p style="margin:0 0 18px 0;font-size:15px;line-height:1.6;">We received a request to reset your account password. Click the button below to continue.</p>

                                <p style="margin:0 0 20px 0;">
                                    <a href="{{ $resetUrl }}" style="display:inline-block;background-color:#2563eb;color:#ffffff;text-decoration:none;font-weight:600;padding:12px 18px;border-radius:8px;">Reset Password</a>
                                </p>

                                <p style="margin:0 0 10px 0;font-size:14px;line-height:1.6;color:#4b5563;">If you did not request this, no further action is required.</p>
                                <p style="margin:0 0 6px 0;font-size:13px;line-height:1.6;color:#6b7280;word-break:break-word;">If the button above does not work, copy and paste this URL into your browser:</p>
                                <p style="margin:0;font-size:13px;line-height:1.6;color:#2563eb;word-break:break-word;">{{ $resetUrl }}</p>
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
