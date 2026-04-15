<x-layouts.app title="Submit Excuse Request">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="excuses" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-6 lg:p-8">
                <div class="max-w-lg">

                    <div class="d d1 mb-6">
                        <a href="{{ route('student.excuses.index') }}" class="inline-flex items-center gap-1.5 text-sm text-base-content/40 hover:text-base-content transition-colors mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                <path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Back to Requests
                        </a>
                        <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Student</p>
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight">Submit Excuse Request</h1>
                        <p class="mt-1 text-sm text-base-content/50">Request an excusal for a date. Upload a signed letter from your parent/guardian.</p>
                    </div>

                    @if ($classes->isEmpty())
                        <div class="d d2">
                            <x-ui.empty-state
                                icon="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"
                                title="No active classes found"
                                description="You need to be enrolled in at least one active class to submit a request."
                            />
                        </div>
                    @else
                        <form method="POST" action="{{ route('student.excuses.store') }}" enctype="multipart/form-data" class="d d2 af-card overflow-hidden !p-0">
                            @csrf
                            <div class="px-5 py-4 border-b af-divider">
                                <h2 class="font-semibold text-sm">Request Details</h2>
                            </div>
                            <div class="px-5 py-5 space-y-5">

                                <x-form.field name="class_id" label="Class" required>
                                    <select
                                        id="class_id"
                                        name="class_id"
                                        class="af-input @error('class_id') af-input-error @enderror"
                                        required
                                    >
                                        <option value="" disabled {{ old('class_id') ? '' : 'selected' }}>Select a class...</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('class_id') === $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}{{ $class->section ? ' — ' . $class->section : '' }} · {{ $class->teacher->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </x-form.field>

                                <x-form.field name="excuse_date" label="Excuse Date" required>
                                    <input
                                        id="excuse_date"
                                        type="date"
                                        name="excuse_date"
                                        value="{{ old('excuse_date') }}"
                                        min="{{ now()->format('Y-m-d') }}"
                                        class="af-input @error('excuse_date') af-input-error @enderror"
                                        required
                                    >
                                </x-form.field>

                                <x-form.field name="reason" label="Reason" required>
                                    <textarea
                                        id="reason"
                                        name="reason"
                                        rows="3"
                                        class="af-input @error('reason') af-input-error @enderror"
                                        placeholder="Briefly explain the reason for your absence..."
                                        maxlength="1000"
                                        required
                                    >{{ old('reason') }}</textarea>
                                </x-form.field>

                                <x-form.field name="document" label="Signed Letter (PDF)" required>
                                    <input
                                        id="document"
                                        type="file"
                                        name="document"
                                        accept=".pdf"
                                        class="af-input file:mr-3 file:border-0 file:bg-primary/10 file:text-primary file:text-xs file:font-semibold file:px-3 file:py-1 file:rounded-lg @error('document') af-input-error @enderror"
                                        required
                                    >
                                    <p class="mt-1.5 text-xs text-base-content/40">PDF only, max 5 MB. Upload a letter signed by your parent or guardian.</p>
                                </x-form.field>

                                <div class="flex justify-end gap-3 pt-2">
                                    <x-ui.button href="{{ route('student.excuses.index') }}" variant="ghost">Cancel</x-ui.button>
                                    <x-ui.button type="submit" variant="primary">Submit Request</x-ui.button>
                                </div>

                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </main>
    </div>
</x-layouts.app>
