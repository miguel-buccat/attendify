<?php

namespace App\Http\Controllers\Teacher;

use App\Enums\ExcuseRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\ReviewExcuseRequest;
use App\Models\ActivityLog;
use App\Models\ExcuseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExcuseRequestController extends Controller
{
    public function index(): View
    {
        $teacher = auth()->user();

        $classIds = $teacher->classes()->pluck('id');

        $excuseRequests = ExcuseRequest::whereIn('class_id', $classIds)
            ->with(['student', 'reviewer', 'schoolClass'])
            ->orderByRaw("case when status = 'Pending' then 0 else 1 end")
            ->orderByDesc('created_at')
            ->get();

        return view('teacher.excuses.index', compact('excuseRequests'));
    }

    public function review(ReviewExcuseRequest $request, ExcuseRequest $excuseRequest): RedirectResponse
    {
        Gate::authorize('review', $excuseRequest);

        $validated = $request->validated();

        $excuseRequest->update([
            'status' => $validated['status'],
            'reviewer_notes' => $validated['reviewer_notes'] ?? null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $statusLabel = $validated['status'] === ExcuseRequestStatus::Acknowledged->value ? 'acknowledged' : 'rejected';

        ActivityLog::log('reviewed_excuse', "Excuse request {$statusLabel} for {$excuseRequest->student->name}", $excuseRequest);

        return redirect()->route('teacher.excuses.index')
            ->with('success', "Excuse request {$statusLabel}.");
    }

    public function download(ExcuseRequest $excuseRequest): BinaryFileResponse
    {
        Gate::authorize('review', $excuseRequest);

        return response()->file(storage_path('app/private/'.$excuseRequest->document_path));
    }
}
