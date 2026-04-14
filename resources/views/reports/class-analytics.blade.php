<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendify — Class Analytics Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10px; color: #1f2937; line-height: 1.5; }

        .header { background-color: #7c3aed; color: #fff; padding: 24px 28px; }
        .header-flex { display: table; width: 100%; }
        .header-left { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }
        .logo-img { height: 32px; width: 32px; border-radius: 6px; vertical-align: middle; margin-right: 8px; }
        .logo-text { font-size: 20px; font-weight: 800; letter-spacing: -0.5px; display: inline; vertical-align: middle; }
        .logo-sub { font-size: 9px; opacity: 0.85; margin-top: 2px; letter-spacing: 0.5px; }
        .report-title { font-size: 13px; font-weight: 700; }
        .report-meta { font-size: 9px; opacity: 0.8; margin-top: 2px; }

        .content { padding: 20px 28px; }

        .institution-note { font-size: 9px; color: #6b7280; margin-bottom: 14px; padding: 6px 10px; background: #f9fafb; border-radius: 6px; border-left: 3px solid #a855f7; }

        .class-info { margin-bottom: 16px; }
        .class-name { font-size: 16px; font-weight: 800; color: #1f2937; }
        .class-details { font-size: 10px; color: #6b7280; margin-top: 2px; }

        .stats-row { display: table; width: 100%; margin-bottom: 16px; }
        .stat-item { display: table-cell; text-align: center; padding: 10px 4px; background: #f9fafb; border: 1px solid #e5e7eb; }
        .stat-item:first-child { border-radius: 6px 0 0 6px; }
        .stat-item:last-child { border-radius: 0 6px 6px 0; }
        .stat-num { font-size: 18px; font-weight: 800; }
        .stat-num.green { color: #16a34a; }
        .stat-lbl { font-size: 8px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; margin-top: 1px; }

        .section-title { font-size: 12px; font-weight: 700; color: #1f2937; margin-bottom: 8px; padding-bottom: 5px; border-bottom: 2px solid #a855f7; }

        table.data { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 9px; }
        table.data th { background: #f3f4f6; color: #374151; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; font-size: 8px; padding: 6px 6px; text-align: left; border-bottom: 2px solid #d1d5db; }
        table.data td { padding: 5px 6px; border-bottom: 1px solid #e5e7eb; }
        table.data tr:nth-child(even) { background: #f9fafb; }

        .badge { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 8px; font-weight: 700; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-gray { background: #f3f4f6; color: #6b7280; }

        .bar-bg { display: inline-block; width: 60px; height: 6px; background: #e5e7eb; border-radius: 3px; vertical-align: middle; }
        .bar-fill { display: block; height: 6px; border-radius: 3px; }
        .bar-green { background: #16a34a; }
        .bar-yellow { background: #ca8a04; }
        .bar-red { background: #dc2626; }

        .footer { margin-top: 24px; padding: 14px 28px; border-top: 1px solid #e5e7eb; }
        .disclaimer { font-size: 8px; color: #9ca3af; line-height: 1.6; }
        .copyright { font-size: 7px; color: #9ca3af; text-align: center; margin-top: 10px; padding-top: 8px; border-top: 1px solid #f3f4f6; }

        .page-break { page-break-before: always; }
        .status-P { color: #16a34a; font-weight: 700; }
        .status-L { color: #ca8a04; font-weight: 700; }
        .status-A { color: #dc2626; font-weight: 700; }
        .status-E { color: #2563eb; font-weight: 700; }
        .status-N { color: #9ca3af; }
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
                <div class="report-title">Class Analytics Report</div>
                <div class="report-meta">Generated: {{ now()->format('M d, Y g:i A') }}</div>
            </div>
        </div>
    </div>

    <div class="content">

        {{-- Institution --}}
        <div class="institution-note">
            Institution: <strong>{{ $institutionName }}</strong>
        </div>

        {{-- Class Info --}}
        <div class="class-info">
            <div class="class-name">{{ $class->name }}</div>
            <div class="class-details">
                Teacher: {{ $class->teacher->name }}
                @if ($class->section) &bull; Section: {{ $class->section }} @endif
                &bull; Status: {{ $class->status->value }}
            </div>
        </div>

        {{-- Summary Stats --}}
        <div class="stats-row">
            <div class="stat-item">
                <div class="stat-num">{{ $totalSessions }}</div>
                <div class="stat-lbl">Total Sessions</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">{{ $students->count() }}</div>
                <div class="stat-lbl">Enrolled Students</div>
            </div>
            <div class="stat-item">
                <div class="stat-num green">
                    @php
                        $allPresent = collect($matrix)->sum('present');
                        $allLate = collect($matrix)->sum('late');
                        $allAbsent = collect($matrix)->sum('absent');
                        $allTotal = $allPresent + $allLate + $allAbsent + collect($matrix)->sum('excused');
                        $overallRate = $allTotal > 0 ? round(($allPresent + $allLate) / $allTotal * 100, 1) : 0;
                    @endphp
                    {{ $overallRate }}%
                </div>
                <div class="stat-lbl">Overall Rate</div>
            </div>
        </div>

        {{-- Student Summary Table --}}
        <div class="section-title">Student Attendance Summary</div>
        <table class="data">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th style="width: 45px;">Present</th>
                    <th style="width: 35px;">Late</th>
                    <th style="width: 45px;">Absent</th>
                    <th style="width: 50px;">Excused</th>
                    <th style="width: 120px;">Attendance Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($matrix as $studentId => $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td style="font-weight: 600;">{{ $row['name'] }}</td>
                        <td>{{ $row['email'] }}</td>
                        <td><span class="status-P">{{ $row['present'] }}</span></td>
                        <td><span class="status-L">{{ $row['late'] }}</span></td>
                        <td><span class="status-A">{{ $row['absent'] }}</span></td>
                        <td><span class="status-E">{{ $row['excused'] }}</span></td>
                        <td>
                            <span class="bar-bg">
                                <span class="bar-fill {{ $row['rate'] >= 80 ? 'bar-green' : ($row['rate'] >= 60 ? 'bar-yellow' : 'bar-red') }}"
                                      style="width: {{ $row['rate'] }}%;"></span>
                            </span>
                            <strong style="color: {{ $row['rate'] >= 80 ? '#16a34a' : ($row['rate'] >= 60 ? '#ca8a04' : '#dc2626') }};">
                                {{ $row['rate'] }}%
                            </strong>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Session-by-Session Detail --}}
        @if ($sessions->count() > 0)
            <div class="page-break"></div>
            <div class="section-title">Session-by-Session Detail</div>
            <table class="data">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Modality</th>
                        <th>Status</th>
                        @foreach ($students as $student)
                            <th style="text-align: center; font-size: 7px; max-width: 50px; word-wrap: break-word;">{{ explode(' ', $student->name)[0] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sessions as $session)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $session->start_time->format('M d, Y') }}</td>
                            <td>{{ $session->start_time->format('g:i A') }}</td>
                            <td>{{ $session->modality->value }}</td>
                            <td>
                                @php
                                    $sc = match($session->status->value) {
                                        'Completed' => 'badge-green',
                                        'Active' => 'badge-blue',
                                        'Cancelled' => 'badge-red',
                                        default => 'badge-gray',
                                    };
                                @endphp
                                <span class="badge {{ $sc }}">{{ $session->status->value }}</span>
                            </td>
                            @foreach ($students as $student)
                                @php
                                    $status = $matrix[$student->id]['sessions'][$session->id] ?? '—';
                                    $statusClass = match($status) {
                                        'Present' => 'status-P',
                                        'Late' => 'status-L',
                                        'Absent' => 'status-A',
                                        'Excused' => 'status-E',
                                        default => 'status-N',
                                    };
                                    $statusAbbr = match($status) {
                                        'Present' => 'P',
                                        'Late' => 'L',
                                        'Absent' => 'A',
                                        'Excused' => 'E',
                                        default => '—',
                                    };
                                @endphp
                                <td style="text-align: center;"><span class="{{ $statusClass }}">{{ $statusAbbr }}</span></td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="disclaimer">
            <strong>System-Generated Report</strong><br>
            This report was automatically generated by Attendify and reflects attendance data as of the date and time shown above. Always verify critical data.
        </div>
        <div class="copyright">
            Attendify licensed under the MIT License. Copyright &copy; {{ date('Y') }} Attendify Developers.
        </div>
    </div>

</body>
</html>
