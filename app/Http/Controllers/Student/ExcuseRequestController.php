<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreExcuseRequest;
use App\Models\ExcuseRequest;
use App\Notifications\ExcuseSubmittedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExcuseRequestController extends Controller
{
    public function index(): View
    {
        $excuseRequests = auth()->user()->excuseRequests()
            ->with(['reviewer', 'schoolClass.teacher'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('student.excuses.index', compact('excuseRequests'));
    }

    public function create(): View
    {
        $classes = auth()->user()->enrolledClasses()
            ->with('teacher:id,name')
            ->active()
            ->orderBy('name')
            ->get();

        return view('student.excuses.create', compact('classes'));
    }

    public function store(StoreExcuseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $path = $request->file('document')->store('excuse-documents', 'local');

        $excuse = ExcuseRequest::create([
            'student_id' => auth()->id(),
            'class_id' => $validated['class_id'],
            'excuse_date' => $validated['excuse_date'],
            'reason' => $validated['reason'],
            'document_path' => $path,
        ]);

        $excuse->load('student', 'schoolClass.teacher');
        $excuse->schoolClass->teacher->notify(new ExcuseSubmittedNotification($excuse));

        return redirect()->route('student.excuses.index')
            ->with('success', 'Excuse request submitted successfully.');
    }

    public function download(ExcuseRequest $excuseRequest): BinaryFileResponse
    {
        abort_unless(
            $excuseRequest->student_id === auth()->id(),
            403,
        );

        return response()->file(storage_path('app/private/'.$excuseRequest->document_path));
    }
}
