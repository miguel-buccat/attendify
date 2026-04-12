<x-layouts.app title="Excuse Requests">
    <style>
        @keyframes d-up { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        .d { animation: d-up .45s cubic-bezier(.16,1,.3,1) both; }
        .d1 { animation-delay: .00s; } .d2 { animation-delay: .07s; } .d3 { animation-delay: .14s; }
        .d4 { animation-delay: .21s; } .d5 { animation-delay: .28s; } .d6 { animation-delay: .35s; }
    </style>
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
                    <div class="d d2 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                        <div class="py-10 flex flex-col items-center gap-2 text-center px-6">
                            <div class="size-14 rounded-2xl bg-base-200 flex items-center justify-center mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-7 text-base-content/30" aria-hidden="true">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M14 2v6h6M16 13H8m8 4H8m2-8H8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <p class="font-semibold text-base-content/60">No excuse requests</p>
                            <p class="text-sm text-base-content/40">Student excuse requests will appear here.</p>
                        </div>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($excuseRequests as $i => $request)
                            @php
                                $statusPill = match ($request->status->value) {
                                    'Acknowledged' => 'text-success bg-success/10 border-success/20',
                                    'Rejected'     => 'text-error bg-error/10 border-error/20',
                                    default        => 'text-warning bg-warning/10 border-warning/20',
                                };
                                $isPending = $request->status === \App\Enums\ExcuseRequestStatus::Pending;
                            @endphp
                            <div class="d d{{ min($i + 2, 6) }} rounded-2xl border {{ $isPending ? 'border-warning/30' : 'border-base-300/50' }} bg-base-100 overflow-hidden">
                                {{-- Card header --}}
                                <div class="px-5 py-4 border-b border-base-300/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="font-semibold">{{ $request->student->name }}</p>
                                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $statusPill }}">{{ $request->status->value }}</span>
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
                                        <form method="POST" action="{{ route('teacher.excuses.review', $request) }}" class="border-t border-base-300/30 pt-4 space-y-3">
                                            @csrf
                                            @method('PATCH')
                                            <div>
                                                <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5">Notes (optional)</label>
                                                <textarea name="reviewer_notes" rows="2" class="w-full rounded-xl border border-base-300/70 bg-base-100 px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40" placeholder="Add a note..." maxlength="500"></textarea>
                                            </div>
                                            <div class="flex items-center gap-2 justify-end">
                                                <button type="submit" name="status" value="Rejected" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-semibold text-error bg-error/10 border border-error/20 hover:bg-error/15 transition-colors">
                                                    Reject
                                                </button>
                                                <button type="submit" name="status" value="Acknowledged" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-xl text-sm font-semibold bg-primary text-primary-content hover:opacity-90 transition-opacity">
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
