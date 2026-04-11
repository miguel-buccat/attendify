<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ClassEnrollmentController extends Controller
{
    public function index(): View
    {
        $classes = auth()->user()->enrolledClasses()
            ->withCount('students')
            ->orderByDesc('pivot_enrolled_at')
            ->get();

        return view('student.classes.index', compact('classes'));
    }
}
