<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendify — Session Attendance Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #1f2937; line-height: 1.5; }

        .header { background-color: #7c3aed; color: #fff; padding: 28px 32px; }
        .header-flex { display: table; width: 100%; }
        .header-left { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }
        .logo-img { height: 36px; width: 36px; border-radius: 8px; vertical-align: middle; margin-right: 10px; }
        .logo-text { font-size: 22px; font-weight: 800; letter-spacing: -0.5px; display: inline; vertical-align: middle; }
        .logo-sub { font-size: 10px; opacity: 0.85; margin-top: 2px; letter-spacing: 0.5px; }
        .report-title { font-size: 14px; font-weight: 700; }
        .report-date { font-size: 10px; opacity: 0.8; margin-top: 3px; }

        .content { padding: 24px 32px; }

        .session-info { margin-bottom: 20px; padding: 12px 16px; background: #f9fafb; border-radius: 6px; border-left: 3px solid #a855f7; }
        .session-info-row { display: table; width: 100%; margin-bottom: 4px; }
        .session-info-label { display: table-cell; width: 120px; font-weight: 700; color: #6b7280; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; padding: 2px 0; }
        .session-info-value { display: table-cell; font-size: 11px; color: #1f2937; padding: 2px 0; }

        .section-title { font-size: 13px; font-weight: 700; color: #1f2937; margin-bottom: 10px; padding-bottom: 6px; border-bottom: 2px solid #a855f7; }

        table.data { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10px; }
        table.data th { background: #f3f4f6; color: #374151; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; font-size: 9px; padding: 8px 10px; text-align: left; border-bottom: 2px solid #d1d5db; }
        table.data td { padding: 6px 10px; border-bottom: 1px solid #e5e7eb; }
        table.data tr:nth-child(even) { background: #f9fafb; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: 700; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-gray { background: #f3f4f6; color: #6b7280; }

        .summary { display: table; width: 100%; margin-bottom: 20px; }
        .summary-item { display: table-cell; text-align: center; padding: 10px 6px; background: #f9fafb; border: 1px solid #e5e7eb; }
        .summary-item:first-child { border-radius: 6px 0 0 6px; }
        .summary-item:last-child { border-radius: 0 6px 6px 0; }
        .summary-value { font-size: 18px; font-weight: 800; }
        .summary-value.green { color: #16a34a; }
        .summary-value.yellow { color: #ca8a04; }
        .summary-value.red { color: #dc2626; }
        .summary-value.blue { color: #2563eb; }
        .summary-value.gray { color: #6b7280; }
        .summary-label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-top: 2px; }

        .footer { margin-top: 30px; padding: 16px 32px; border-top: 1px solid #e5e7eb; }
        .disclaimer { font-size: 9px; color: #9ca3af; line-height: 1.6; }
        .copyright { font-size: 8px; color: #9ca3af; text-align: center; margin-top: 12px; padding-top: 10px; border-top: 1px solid #f3f4f6; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <div class="header-flex">
            <div class="header-left">
                <img src="{{ public_path('assets/attendify.png') }}" class="logo-img" alt="Attendify">
                <span class="logo-text">Attendify</span>
                <div class="logo-sub">Attendance Monitoring System</div>
            </div>
            <div class="header-right">
                <div class="report-title">Session Attendance Report</div>
                <div class="report-date">{{ $session->start_time->format('M d, Y') }}</div>
                <div class="report-date">Generated: {{ now()->format('M d, Y g:i A') }}</div>
            </div>
        </div>
    </div>

    <div class="content">

        {{-- Session Details --}}
        <div class="session-info">
            <div class="session-info-row">
                <div class="session-info-label">Class</div>
                <div class="session-info-value">{{ $session->schoolClass->name }}</div>
            </div>
            <div class="session-info-row">
                <div class="session-info-label">Teacher</div>
                <div class="session-info-value">{{ $session->schoolClass->teacher?->name ?? 'N/A' }}</div>
            </div>
            <div class="session-info-row">
                <div class="session-info-label">Date &amp; Time</div>
                <div class="session-info-value">{{ $session->start_time->format('M d, Y — g:i A') }} to {{ $session->end_time->format('g:i A') }}</div>
            </div>
            <div class="session-info-row">
                <div class="session-info-label">Modality</div>
                <div class="session-info-value">{{ $session->modality->value }}</div>
            </div>
            @if ($session->location)
                <div class="session-info-row">
                    <div class="session-info-label">Location</div>
                    <div class="session-info-value">{{ $session->location }}</div>
                </div>
            @endif
            <div class="session-info-row">
                <div class="session-info-label">Status</div>
                <div class="session-info-value">{{ $session->status->value }}</div>
            </div>
        </div>

        {{-- Summary --}}
        <div class="summary">
            <div class="summary-item">
                <div class="summary-value green">{{ $presentCount }}</div>
                <div class="summary-label">Present</div>
            </div>
            <div class="summary-item">
                <div class="summary-value yellow">{{ $lateCount }}</div>
                <div class="summary-label">Late</div>
            </div>
            <div class="summary-item">
                <div class="summary-value red">{{ $absentCount }}</div>
                <div class="summary-label">Absent</div>
            </div>
            <div class="summary-item">
                <div class="summary-value blue">{{ $excusedCount }}</div>
                <div class="summary-label">Excused</div>
            </div>
            <div class="summary-item">
                <div class="summary-value gray">{{ $noRecordCount }}</div>
                <div class="summary-label">No Record</div>
            </div>
        </div>

        {{-- Attendance Table --}}
        <div class="section-title">Student Attendance</div>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 30px;">#</th>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th style="width: 70px;">Status</th>
                    <th style="width: 80px;">Scanned At</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $index => $student)
                    @php
                        $record = $recordsByStudent->get($student->id);
                        $bc = match($record?->status->value ?? null) {
                            'Present' => 'badge-green',
                            'Late' => 'badge-yellow',
                            'Absent' => 'badge-red',
                            'Excused' => 'badge-blue',
                            default => 'badge-gray',
                        };
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="font-weight: 600;">{{ $student->name }}</td>
                        <td>{{ $student->email }}</td>
                        <td><span class="badge {{ $bc }}">{{ $record?->status->value ?? 'No Record' }}</span></td>
                        <td>{{ $record?->scanned_at?->format('g:i A') ?? '—' }}</td>
                        <td>{{ $record?->notes ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="disclaimer">
            <strong>System-Generated Report — Do Not Alter</strong><br>
            This report was automatically generated by Attendify and reflects attendance data as of the date and time shown above. Always verify critical data.
        </div>
        <div class="copyright">
            Attendify licensed under the MIT License. Copyright &copy; {{ date('Y') }} Attendify Developers.
        </div>
    </div>

</body>
</html>
