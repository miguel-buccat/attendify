<x-layouts.app title="My Excuse Requests">
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

                <div class="d d1 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Student</p>
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight">Excuse Requests</h1>
                        <p class="mt-1 text-sm text-base-content/50">Submit and track your excuse requests.</p>
                    </div>
                    <a href="{{ route('student.excuses.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-primary-content text-sm font-semibold hover:opacity-90 transition-opacity self-start">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                            <path d="M12 5v14m-7-7h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        New Request
                    </a>
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
                            <p class="font-semibold text-base-content/60">No excuse requests yet</p>
                            <p class="text-sm text-base-content/40">Submit an excuse request for a missed session.</p>
                        </div>
                    </div>
                @else
                    <div class="d d2 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                        <div class="px-5 py-4 border-b border-base-300/30 flex items-center justify-between">
                            <h2 class="font-semibold text-sm">Your Requests</h2>
                            <span class="text-xs text-base-content/40">{{ $excuseRequests->count() }} total</span>
                        </div>
                        <div class="divide-y divide-base-300/30">
                            @foreach ($excuseRequests as $request)
                                @php
                                    $statusPill = match ($request->status->value) {
                                        'Acknowledged' => 'text-success bg-success/10 border-success/20',
                                        'Rejected'     => 'text-error bg-error/10 border-error/20',
                                        default        => 'text-warning bg-warning/10 border-warning/20',
                                    };
                                @endphp
                                <div class="px-5 py-3 hover:bg-base-200/40 transition-colors">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-primary/80">{{ $request->schoolClass->name }}</p>
                                            <p class="text-sm font-medium mt-0.5">{{ $request->excuse_date->format('M d, Y') }}</p>
                                            <p class="text-xs text-base-content/50 mt-1 line-clamp-2">{{ $request->reason }}</p>
                                            @if ($request->reviewer_notes)
                                                <p class="text-xs text-base-content/40 italic mt-1">Note: {{ $request->reviewer_notes }}</p>
                                            @endif
                                            <p class="text-xs text-base-content/30 mt-1">Submitted {{ $request->created_at->format('M d, Y') }}</p>
                                        </div>
                                        <div class="shrink-0 flex flex-col items-end gap-1.5">
                                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $statusPill }}">{{ $request->status->value }}</span>
                                            @if ($request->reviewer)
                                                <span class="text-xs text-base-content/35">by {{ $request->reviewer->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </main>
    </div>
</x-layouts.app>
