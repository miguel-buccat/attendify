<x-layouts.app title="Excuse Requests">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="excuses" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">

                @if (session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                <div>
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold">Excuse Requests</h1>
                    <p class="mt-1 text-sm text-base-content/60">Review excuse requests from your students.</p>
                </div>

                @if ($excuseRequests->isEmpty())
                    <div class="card bg-base-100 rounded-xl border border-base-300">
                        <div class="card-body items-center text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-12 text-base-content/20 mb-2" aria-hidden="true">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14 2v6h6M16 13H8m8 4H8m2-8H8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <p class="text-base-content/50 font-medium">No excuse requests.</p>
                            <p class="text-sm text-base-content/40 mt-1">Student excuse requests will appear here.</p>
                        </div>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($excuseRequests as $request)
                            @php
                                $statusBadge = match ($request->status->value) {
                                    'Acknowledged' => 'badge-success',
                                    'Rejected' => 'badge-error',
                                    default => 'badge-warning',
                                };
                                $isPending = $request->status === \App\Enums\ExcuseRequestStatus::Pending;
                            @endphp
                            <div class="card bg-base-100 rounded-xl border border-base-300 {{ $isPending ? 'border-warning/30' : '' }}">
                                <div class="card-body gap-4 p-4 sm:p-6">
                                    {{-- Header --}}
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="font-semibold">{{ $request->student->name }}</p>
                                                <span class="badge {{ $statusBadge }} badge-sm">{{ $request->status->value }}</span>
                                            </div>
                                            <p class="text-sm text-base-content/60 mt-0.5">
                                                {{ $request->schoolClass->name }} · {{ $request->student->email }}
                                            </p>
                                        </div>
                                        <div class="text-sm text-base-content/60 shrink-0">
                                            <p class="font-medium">Date: {{ $request->excuse_date->format('M d, Y') }}</p>
                                            <p class="text-xs mt-0.5">Submitted {{ $request->created_at->format('M d, Y g:i A') }}</p>
                                        </div>
                                    </div>

                                    {{-- Reason --}}
                                    <div>
                                        <p class="text-xs font-medium text-base-content/50 uppercase tracking-wider mb-1">Reason</p>
                                        <p class="text-sm text-base-content/80">{{ $request->reason }}</p>
                                    </div>

                                    {{-- Document link --}}
                                    <div>
                                        <a href="{{ route('teacher.excuses.download', $request) }}" target="_blank" class="inline-flex items-center gap-1.5 text-sm text-primary hover:underline">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M14 2v6h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            View Document (PDF)
                                        </a>
                                    </div>

                                    {{-- Reviewer notes (if already reviewed) --}}
                                    @if ($request->reviewer_notes)
                                        <div>
                                            <p class="text-xs font-medium text-base-content/50 uppercase tracking-wider mb-1">Reviewer Notes</p>
                                            <p class="text-sm text-base-content/80 italic">{{ $request->reviewer_notes }}</p>
                                            @if ($request->reviewer)
                                                <p class="text-xs text-base-content/40 mt-1">— {{ $request->reviewer->name }}, {{ $request->reviewed_at->format('M d, Y g:i A') }}</p>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Action form (only for pending) --}}
                                    @if ($isPending)
                                        <form method="POST" action="{{ route('teacher.excuses.review', $request) }}" class="border-t border-base-200 pt-4 space-y-3">
                                            @csrf
                                            @method('PATCH')

                                            <div class="form-control">
                                                <label class="label pb-1">
                                                    <span class="label-text text-sm">Notes (optional)</span>
                                                </label>
                                                <textarea name="reviewer_notes" rows="2" class="textarea textarea-bordered textarea-sm w-full rounded-lg" placeholder="Add a note..." maxlength="500"></textarea>
                                            </div>

                                            <div class="flex items-center gap-2 justify-end">
                                                <button type="submit" name="status" value="Rejected" class="btn btn-error btn-sm rounded-lg btn-outline">
                                                    Reject
                                                </button>
                                                <button type="submit" name="status" value="Acknowledged" class="btn btn-success btn-sm rounded-lg">
                                                    Acknowledge
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </main>
    </div>
</x-layouts.app>
