<x-layouts.app title="Submit Excuse Request">
    <style>
        @keyframes d-up { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        .d { animation: d-up .45s cubic-bezier(.16,1,.3,1) both; }
        .d1 { animation-delay: .00s; } .d2 { animation-delay: .07s; }
    </style>
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="excuses" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8">
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
                        <div class="d d2 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                            <div class="py-10 flex flex-col items-center gap-2 text-center px-6">
                                <p class="font-semibold text-base-content/60">No active classes found</p>
                                <p class="text-sm text-base-content/40">You need to be enrolled in at least one active class to submit a request.</p>
                            </div>
                        </div>
                    @else
                        <form method="POST" action="{{ route('student.excuses.store') }}" enctype="multipart/form-data" class="d d2 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                            @csrf
                            <div class="px-5 py-4 border-b border-base-300/30">
                                <h2 class="font-semibold text-sm">Request Details</h2>
                            </div>
                            <div class="px-5 py-5 space-y-5">

                                <div>
                                    <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="class_id">Class <span class="text-error">*</span></label>
                                    <select
                                        id="class_id"
                                        name="class_id"
                                        class="w-full rounded-xl border {{ $errors->has('class_id') ? 'border-error' : 'border-base-300/70' }} bg-base-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40"
                                        required
                                    >
                                        <option value="" disabled {{ old('class_id') ? '' : 'selected' }}>Select a class...</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('class_id') === $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}{{ $class->section ? ' — ' . $class->section : '' }} · {{ $class->teacher->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="excuse_date">Excuse Date <span class="text-error">*</span></label>
                                    <input
                                        id="excuse_date"
                                        type="date"
                                        name="excuse_date"
                                        value="{{ old('excuse_date') }}"
                                        min="{{ now()->format('Y-m-d') }}"
                                        class="w-full rounded-xl border {{ $errors->has('excuse_date') ? 'border-error' : 'border-base-300/70' }} bg-base-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40"
                                        required
                                    >
                                    @error('excuse_date')
                                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="reason">Reason <span class="text-error">*</span></label>
                                    <textarea
                                        id="reason"
                                        name="reason"
                                        rows="3"
                                        class="w-full rounded-xl border {{ $errors->has('reason') ? 'border-error' : 'border-base-300/70' }} bg-base-100 px-3 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40"
                                        placeholder="Briefly explain the reason for your absence..."
                                        maxlength="1000"
                                        required
                                    >{{ old('reason') }}</textarea>
                                    @error('reason')
                                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="document">Signed Letter (PDF) <span class="text-error">*</span></label>
                                    <input
                                        id="document"
                                        type="file"
                                        name="document"
                                        accept=".pdf"
                                        class="w-full rounded-xl border {{ $errors->has('document') ? 'border-error' : 'border-base-300/70' }} bg-base-100 px-3 py-2 text-sm file:mr-3 file:border-0 file:bg-primary/10 file:text-primary file:text-xs file:font-semibold file:px-3 file:py-1 file:rounded-lg focus:outline-none"
                                        required
                                    >
                                    <p class="mt-1.5 text-xs text-base-content/40">PDF only, max 5 MB. Upload a letter signed by your parent or guardian.</p>
                                    @error('document')
                                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex justify-end gap-3 pt-2">
                                    <a href="{{ route('student.excuses.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-base-200 text-base-content/60 border border-base-300/50 text-sm font-medium hover:bg-base-300/50 transition-colors">
                                        Cancel
                                    </a>
                                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-primary-content text-sm font-semibold hover:opacity-90 transition-opacity">
                                        Submit Request
                                    </button>
                                </div>

                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </main>
    </div>
</x-layouts.app>
