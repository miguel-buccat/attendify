<x-layouts.app title="My Excuse Requests">
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
                    @if ($classes->isNotEmpty())
                        <button type="button" onclick="document.getElementById('create-excuse-modal').showModal()" class="af-btn af-btn-primary self-start">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                <path d="M12 5v14m-7-7h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                            New Request
                        </button>
                    @endif
                </div>

                @if ($excuseRequests->isEmpty())
                    <div class="d d2">
                        <x-ui.empty-state
                            icon="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z M14 2v6h6 M16 13H8m8 4H8m2-8H8"
                            title="No excuse requests yet"
                            description="Submit an excuse request for a missed session."
                        />
                    </div>
                @else
                    <div class="d d2 af-card overflow-hidden !p-0">
                        <div class="px-5 py-4 border-b af-divider flex items-center justify-between">
                            <h2 class="font-semibold text-sm">Your Requests</h2>
                            <span class="text-xs text-base-content/40">{{ $excuseRequests->total() }} total</span>
                        </div>
                        <div class="divide-y divide-base-content/6">
                            @foreach ($excuseRequests as $request)
                                @php
                                    $statusVariant = match ($request->status->value) {
                                        'Acknowledged' => 'success',
                                        'Rejected'     => 'error',
                                        default        => 'warning',
                                    };
                                @endphp
                                <div class="px-5 py-3 hover:bg-base-content/[.03] transition-colors">
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
                                            <x-ui.badge :variant="$statusVariant" size="xs">{{ $request->status->value }}</x-ui.badge>
                                            @if ($request->reviewer)
                                                <span class="text-xs text-base-content/35">by {{ $request->reviewer->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if ($excuseRequests->hasPages())
                            <div class="px-5 py-4 border-t af-divider">
                                {{ $excuseRequests->links() }}
                            </div>
                        @endif
                    </div>
                @endif

            </div>
        </main>
    </div>

    {{-- Create Excuse Request Modal --}}
    @if ($classes->isNotEmpty())
    <dialog id="create-excuse-modal" class="modal">
        <div class="af-modal-box modal-box rounded-2xl border border-base-300/30 shadow-2xl max-w-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold tracking-tight">New Excuse Request</h3>
                <form method="dialog">
                    <button class="af-btn af-btn-ghost af-btn-icon af-btn-sm rounded-xl text-base-content/40 hover:text-base-content/70">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                </form>
            </div>

            @if ($errors->any())
                <x-ui.alert variant="error" class="mb-4">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('student.excuses.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <x-form.field name="class_id" label="Class" required>
                    <select name="class_id" class="af-input @error('class_id') af-input-error @enderror" required>
                        <option value="" disabled {{ old('class_id') ? '' : 'selected' }}>Select a class...</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" @selected(old('class_id') == $class->id)>
                                {{ $class->name }}{{ $class->section ? ' — ' . $class->section : '' }} · {{ $class->teacher->name }}
                            </option>
                        @endforeach
                    </select>
                </x-form.field>

                <x-form.field name="excuse_date" label="Excuse Date" required>
                    <input type="date" name="excuse_date" value="{{ old('excuse_date') }}"
                        min="{{ now()->format('Y-m-d') }}"
                        class="af-input @error('excuse_date') af-input-error @enderror" required>
                </x-form.field>

                <x-form.field name="reason" label="Reason" required>
                    <textarea name="reason" rows="3"
                        class="af-input @error('reason') af-input-error @enderror"
                        placeholder="Briefly explain the reason for your absence..."
                        maxlength="1000" required>{{ old('reason') }}</textarea>
                </x-form.field>

                <x-form.field name="document" label="Signed Letter (PDF)" required>
                    <input type="file" name="document" accept=".pdf"
                        class="af-input file:mr-3 file:border-0 file:bg-primary/10 file:text-primary file:text-xs file:font-semibold file:px-3 file:py-1 file:rounded-lg @error('document') af-input-error @enderror"
                        required>
                    <p class="mt-1.5 text-xs text-base-content/40">PDF only, max 5 MB. Upload a letter signed by your parent or guardian.</p>
                </x-form.field>

                <div class="flex justify-end gap-2 pt-1">
                    <x-ui.button type="button" variant="ghost" onclick="this.closest('dialog').close()">Cancel</x-ui.button>
                    <x-ui.button type="submit" variant="primary">Submit Request</x-ui.button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>
    @endif

    @if ($errors->any())
        <script>document.getElementById('create-excuse-modal')?.showModal();</script>
    @endif
</x-layouts.app>
