<x-layouts.app title="My Excuse Requests">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="excuses" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">

                @if (session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold">Excuse Requests</h1>
                        <p class="mt-1 text-sm text-base-content/60">Submit and track your excuse requests.</p>
                    </div>
                    <a href="{{ route('student.excuses.create') }}" class="btn btn-primary btn-sm rounded-lg gap-1.5 self-start">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                            <path d="M12 5v14m-7-7h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        New Request
                    </a>
                </div>

                @if ($excuseRequests->isEmpty())
                    <div class="card bg-base-100 rounded-xl border border-base-300">
                        <div class="card-body items-center text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-12 text-base-content/20 mb-2" aria-hidden="true">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14 2v6h6M16 13H8m8 4H8m2-8H8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <p class="text-base-content/50 font-medium">No excuse requests yet.</p>
                            <p class="text-sm text-base-content/40 mt-1">Submit an excuse request for upcoming dates.</p>
                        </div>
                    </div>
                @else
                    <div class="card bg-base-100 rounded-xl border border-base-300">
                        <div class="card-body gap-0 p-0">

                            {{-- Mobile: card layout --}}
                            <div class="space-y-0 divide-y divide-base-300 sm:hidden">
                                @foreach ($excuseRequests as $request)
                                    @php
                                        $statusBadge = match ($request->status->value) {
                                            'Acknowledged' => 'badge-success',
                                            'Rejected' => 'badge-error',
                                            default => 'badge-warning',
                                        };
                                    @endphp
                                    <div class="px-4 py-3 space-y-1.5">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-sm font-medium">{{ $request->excuse_date->format('M d, Y') }}</p>
                                            <span class="badge {{ $statusBadge }} badge-sm shrink-0">{{ $request->status->value }}</span>
                                        </div>
                                        <p class="text-xs font-medium text-primary/80">{{ $request->schoolClass->name }}</p>
                                        <p class="text-xs text-base-content/60 line-clamp-2">{{ $request->reason }}</p>
                                        @if ($request->reviewer_notes)
                                            <p class="text-xs text-base-content/50 italic">Note: {{ $request->reviewer_notes }}</p>
                                        @endif
                                        <p class="text-xs text-base-content/40">Submitted {{ $request->created_at->format('M d, Y') }}</p>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Desktop: table layout --}}
                            <div class="hidden sm:block overflow-x-auto">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Class</th>
                                            <th>Excuse Date</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Reviewed By</th>
                                            <th>Notes</th>
                                            <th>Submitted</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($excuseRequests as $request)
                                            @php
                                                $statusBadge = match ($request->status->value) {
                                                    'Acknowledged' => 'badge-success',
                                                    'Rejected' => 'badge-error',
                                                    default => 'badge-warning',
                                                };
                                            @endphp
                                            <tr>
                                                <td class="font-medium text-primary/80">{{ $request->schoolClass->name }}</td>
                                                <td class="font-medium">{{ $request->excuse_date->format('M d, Y') }}</td>
                                                <td class="text-base-content/60 max-w-xs truncate">{{ $request->reason }}</td>
                                                <td><span class="badge {{ $statusBadge }} badge-sm">{{ $request->status->value }}</span></td>
                                                <td class="text-base-content/60">{{ $request->reviewer?->name ?? '—' }}</td>
                                                <td class="text-base-content/60 max-w-xs truncate">{{ $request->reviewer_notes ?? '—' }}</td>
                                                <td class="text-base-content/60">{{ $request->created_at->format('M d, Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </main>
    </div>
</x-layouts.app>
