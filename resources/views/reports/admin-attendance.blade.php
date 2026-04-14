<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendify — System Attendance Report</title>
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

        .institution-note { font-size: 10px; color: #6b7280; margin-bottom: 16px; padding: 8px 12px; background: #f9fafb; border-radius: 6px; border-left: 3px solid #a855f7; }

        .stats-grid { display: table; width: 100%; margin-bottom: 20px; }
        .stat-box { display: table-cell; width: 16.66%; text-align: center; padding: 12px 6px; background: #f9fafb; border: 1px solid #e5e7eb; }
        .stat-box:first-child { border-radius: 6px 0 0 6px; }
        .stat-box:last-child { border-radius: 0 6px 6px 0; }
        .stat-value { font-size: 20px; font-weight: 800; color: #1f2937; }
        .stat-value.green { color: #16a34a; }
        .stat-value.yellow { color: #ca8a04; }
        .stat-value.red { color: #dc2626; }
        .stat-value.blue { color: #2563eb; }
        .stat-value.purple { color: #7c3aed; }
        .stat-label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-top: 2px; }

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


        .bar-bg { display: inline-block; width: 80px; height: 8px; background: #e5e7eb; border-radius: 4px; vertical-align: middle; }
        .bar-fill { display: block; height: 8px; border-radius: 4px; }
        .bar-green { background: #16a34a; }
        .bar-yellow { background: #ca8a04; }
        .bar-red { background: #dc2626; }

        .footer { margin-top: 30px; padding: 16px 32px; border-top: 1px solid #e5e7eb; }
        .disclaimer { font-size: 9px; color: #9ca3af; line-height: 1.6; }
        .copyright { font-size: 8px; color: #9ca3af; text-align: center; margin-top: 12px; padding-top: 10px; border-top: 1px solid #f3f4f6; }

        .page-break { page-break-before: always; }
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
                <div class="report-title">System Attendance Report</div>
                <div class="report-date">{{ $from->format('M d, Y') }} — {{ $to->format('M d, Y') }}</div>
                <div class="report-date">Generated: {{ now()->format('M d, Y g:i A') }}</div>
            </div>
        </div>
    </div>

    <div class="content">

        {{-- Institution Note --}}
        <div class="institution-note">
            Institution: <strong>{{ $institutionName }}</strong>
        </div>

        {{-- Summary Stats --}}
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">{{ $totalRecords }}</div>
                <div class="stat-label">Total Records</div>
            </div>
            <div class="stat-box">
                <div class="stat-value green">{{ $presentCount }}</div>
                <div class="stat-label">Present</div>
            </div>
            <div class="stat-box">
                <div class="stat-value yellow">{{ $lateCount }}</div>
                <div class="stat-label">Late</div>
            </div>
            <div class="stat-box">
                <div class="stat-value red">{{ $absentCount }}</div>
                <div class="stat-label">Absent</div>
            </div>
            <div class="stat-box">
                <div class="stat-value blue">{{ $excusedCount }}</div>
                <div class="stat-label">Excused</div>
            </div>
            <div class="stat-box">
                <div class="stat-value purple">{{ $attendanceRate }}%</div>
                <div class="stat-label">Overall Rate</div>
            </div>
        </div>

        {{-- Class Attendance Overview --}}
        <div class="section-title">Class Attendance Overview</div>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 40px;">#</th>
                    <th>Class</th>
                    <th>Teacher</th>
                    <th style="width: 65px;">Students</th>
                    <th style="width: 65px;">Sessions</th>
                    <th style="width: 140px;">Attendance Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($classesRanked as $index => $class)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="font-weight: 600;">{{ $class->name }}</td>
                        <td>{{ $class->teacher?->name ?? 'N/A' }}</td>
                        <td>{{ $class->student_count }}</td>
                        <td>{{ $class->total_sessions }}</td>
                        <td>
                            <span class="bar-bg">
                                <span class="bar-fill {{ $class->attendance_rate >= 80 ? 'bar-green' : ($class->attendance_rate >= 60 ? 'bar-yellow' : 'bar-red') }}"
                                      style="width: {{ $class->attendance_rate }}%;"></span>
                            </span>
                            <strong style="color: {{ $class->attendance_rate >= 80 ? '#16a34a' : ($class->attendance_rate >= 60 ? '#ca8a04' : '#dc2626') }};">
                                {{ $class->attendance_rate }}%
                            </strong>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Detailed Records --}}
        @if ($records->count() > 0)
            <div class="page-break"></div>
            <div class="section-title">Detailed Attendance Records</div>
            <table class="data">
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Session Date</th>
                        <th>Student</th>
                        <th>Email</th>
                        <th style="width: 70px;">Status</th>
                        <th>Scanned At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records->take(500) as $record)
                        <tr>
                            <td>{{ $record->classSession?->schoolClass?->name ?? 'N/A' }}</td>
                            <td>{{ $record->classSession?->start_time?->format('M d, Y g:i A') ?? '' }}</td>
                            <td style="font-weight: 600;">{{ $record->student?->name ?? 'N/A' }}</td>
                            <td>{{ $record->student?->email ?? '' }}</td>
                            <td>
                                @php
                                    $bc = match($record->status->value) {
                                        'Present' => 'badge-green',
                                        'Late' => 'badge-yellow',
                                        'Absent' => 'badge-red',
                                        'Excused' => 'badge-blue',
                                        default => '',
                                    };
                                @endphp
                                <span class="badge {{ $bc }}">{{ $record->status->value }}</span>
                            </td>
                            <td>{{ $record->scanned_at?->format('g:i A') ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($records->count() > 500)
                <p style="font-size: 9px; color: #9ca3af; margin-top: 8px;">Showing first 500 of {{ $records->count() }} records. Export CSV for complete data.</p>
            @endif
        @endif
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
