<?php

namespace App\Http\Controllers\Teacher;

use App\Enums\ClassStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\EnrollStudentsRequest;
use App\Http\Requests\Teacher\StoreClassRequest;
use App\Http\Requests\Teacher\UpdateClassRequest;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(): View
    {
        $classes = auth()->user()->classes()
            ->withCount('students')
            ->orderByDesc('created_at')
            ->get();

        return view('teacher.classes.index', compact('classes'));
    }

    public function create(): View
    {
        return view('teacher.classes.create');
    }

    public function store(StoreClassRequest $request): RedirectResponse
    {
        $class = SchoolClass::create([
            'teacher_id' => auth()->id(),
            ...$request->validated(),
        ]);

        return redirect()->route('teacher.classes.show', $class)
            ->with('success', 'Class created successfully.');
    }

    public function show(SchoolClass $class): View
    {
        Gate::authorize('view', $class);

        $class->load(['students' => fn ($q) => $q->orderBy('name')]);

        return view('teacher.classes.show', compact('class'));
    }

    public function update(UpdateClassRequest $request, SchoolClass $class): RedirectResponse
    {
        Gate::authorize('update', $class);

        $class->update($request->validated());

        return redirect()->route('teacher.classes.show', $class)
            ->with('success', 'Class updated.');
    }

    public function archive(SchoolClass $class): RedirectResponse
    {
        Gate::authorize('archive', $class);

        $class->update(['status' => ClassStatus::Archived]);

        return redirect()->route('teacher.classes.index')
            ->with('success', 'Class archived.');
    }

    public function searchStudents(Request $request, SchoolClass $class): JsonResponse
    {
        Gate::authorize('enroll', $class);

        $query = trim($request->query('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        $enrolledIds = $class->students()->pluck('users.id');

        $students = User::where('role', UserRole::Student)
            ->whereNotIn('id', $enrolledIds)
            ->where(function ($q) use ($query) {
                $q->where('email', 'ilike', "%{$query}%")
                  ->orWhere('name', 'ilike', "%{$query}%");
            })
            ->select('id', 'name', 'email', 'avatar_path')
            ->limit(10)
            ->orderBy('name')
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url,
                'initials' => mb_strtoupper(mb_substr($user->name, 0, 1)),
            ]);

        return response()->json($students);
    }

    public function enroll(EnrollStudentsRequest $request, SchoolClass $class): RedirectResponse
    {
        Gate::authorize('enroll', $class);

        $studentIds = $request->validated('students');
        $enrolled = 0;

        foreach ($studentIds as $studentId) {
            if (! $class->students()->where('student_id', $studentId)->exists()) {
                $class->students()->attach($studentId, ['enrolled_at' => now()]);
                $enrolled++;
            }
        }

        $message = $enrolled === 1
            ? '1 student enrolled.'
            : "{$enrolled} students enrolled.";

        return redirect()->route('teacher.classes.show', $class)
            ->with('success', $message);
    }

    public function unenroll(SchoolClass $class, User $student): RedirectResponse
    {
        Gate::authorize('unenroll', $class);

        $class->students()->detach($student->id);

        return redirect()->route('teacher.classes.show', $class)
            ->with('success', "{$student->name} has been removed from the class.");
    }
}
