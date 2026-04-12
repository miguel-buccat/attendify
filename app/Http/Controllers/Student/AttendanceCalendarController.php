<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AttendanceCalendarController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $records = $user->attendanceRecords()
            ->with('classSession.schoolClass')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($record) => [
                'date' => $record->classSession?->start_time?->format('Y-m-d'),
                'status' => $record->status->value,
                'class' => $record->classSession?->schoolClass?->name ?? 'Unknown',
                'time' => $record->classSession?->start_time?->format('g:i A'),
            ])
            ->groupBy('date');

        return view('student.calendar.index', compact('records'));
    }
}
