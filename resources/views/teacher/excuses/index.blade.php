<x-layouts.app title="Excuse Requests">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="excuses" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">

                @if (session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                <div class="d d1">
                    <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Teacher</p>
                    <h1 class="text-2xl md:text-3xl font-black tracking-tight">Excuse Requests</h1>
                    <p class="mt-1 text-sm text-base-content/50">Review excuse requests from your students.</p>
                </div>

                @if ($excuseRequests->isEmpty())
                    <div class="d d2">
                        <x-ui.empty-state
                            icon="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8ZM14 2v6h6M16 13H8m8 4H8m2-8H8"
                            title="No excuse requests"
                            description="Student excuse requests will appear here."
                        />
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($excuseRequests as $i => $request)
                            @php
                                $statusVariant = match ($request->status->value) {
                                    'Acknowledged' => 'success',
                                    'Rejected'     => 'error',
                                    default        => 'warning',
                                };
                                $isPending = $request->status === \App\Enums\ExcuseRequestStatus::Pending;
                            @endphp
                            <div class="d d{{ min($i + 2, 6) }} af-card {{ $isPending ? '!border-warning/30' : '' }} overflow-hidden !p-0">
                                {{-- Card header --}}
                                <div class="px-5 py-4 border-b af-divider flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="font-semibold">{{ $request->student->name }}</p>
                                            <x-ui.badge :variant="$statusVariant" size="xs">{{ $request->status->value }}</x-ui.badge>
                                        </div>
                                        <p class="text-sm text-base-content/50 mt-0.5">
                                            {{ $request->schoolClass->name }} · {{ $request->student->email }}
                                        </p>
                                    </div>
                                    <div class="text-sm text-base-content/50 shrink-0">
                                        <p class="font-medium text-base-content/70">{{ $request->excuse_date->format('M d, Y') }}</p>
                                        <p class="text-xs mt-0.5">Submitted {{ $request->created_at->format('M d, Y g:i A') }}</p>
                                    </div>
                                </div>

                                {{-- Body --}}
                                <div class="px-5 py-4 space-y-4">
                                    {{-- Reason --}}
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 mb-1">Reason</p>
                                        <p class="text-sm text-base-content/80">{{ $request->reason }}</p>
                                    </div>

                                    {{-- Document --}}
                                    <div>
                                        <a href="{{ route('teacher.excuses.download', $request) }}" target="_blank" class="inline-flex items-center gap-1.5 text-sm text-primary hover:underline">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M14 2v6h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            View Document (PDF)
                                        </a>
                                    </div>

                                    {{-- Reviewer notes --}}
                                    @if ($request->reviewer_notes)
                                        <div class="rounded-xl bg-base-200/50 border border-base-300/50 px-4 py-3">
                                            <p class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 mb-1">Reviewer Notes</p>
                                            <p class="text-sm text-base-content/80 italic">{{ $request->reviewer_notes }}</p>
                                            @if ($request->reviewer)
                                                <p class="text-xs text-base-content/40 mt-1">— {{ $request->reviewer->name }}, {{ $request->reviewed_at->format('M d, Y g:i A') }}</p>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Action form (pending only) --}}
                                    @if ($isPending)
                                        <form method="POST" action="{{ route('teacher.excuses.review', $request) }}" class="border-t af-divider pt-4 space-y-3">
                                            @csrf
                                            @method('PATCH')
                                            <div>
                                                <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5">Notes (optional)</label>
                                                <textarea name="reviewer_notes" rows="2" class="af-input" placeholder="Add a note..." maxlength="500"></textarea>
                                            </div>
                                            <div class="flex items-center gap-2 justify-end">
                                                <x-ui.button type="submit" name="status" value="Rejected" variant="danger" size="sm">Reject</x-ui.button>
                                                <x-ui.button type="submit" name="status" value="Acknowledged" variant="primary" size="sm">Acknowledge</x-ui.button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if ($excuseRequests->hasPages())
                        <div class="mt-4">
                            {{ $excuseRequests->links() }}
                        </div>
                    @endif
                @endif

            </div>
        </main>
    </div>
</x-layouts.app>
