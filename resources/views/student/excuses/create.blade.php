<x-layouts.app title="Submit Excuse Request">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="excuses" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8">
                <div class="max-w-lg">

                    <div class="mb-6">
                        <a href="{{ route('student.excuses.index') }}" class="inline-flex items-center gap-1.5 text-sm text-base-content/60 hover:text-base-content transition-colors mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                <path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Back to Requests
                        </a>
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold">Submit Excuse Request</h1>
                        <p class="mt-1 text-sm text-base-content/60">Request an excusal for an upcoming date. Upload a signed letter from your parent/guardian.</p>
                    </div>

                    @if ($classes->isEmpty())
                        <div class="rounded-xl border border-base-300 bg-base-100 p-8 text-center">
                            <p class="text-base-content/50 font-medium">No active classes found.</p>
                            <p class="text-sm text-base-content/40 mt-1">You need to be enrolled in at least one active class to submit an excuse request.</p>
                        </div>
                    @else
                        <form method="POST" action="{{ route('student.excuses.store') }}" enctype="multipart/form-data" class="rounded-xl border border-base-300 bg-base-100 p-6 space-y-5">
                            @csrf

                            <div class="form-control">
                                <label class="label pb-1" for="class_id">
                                    <span class="label-text font-medium text-sm">Class <span class="text-error">*</span></span>
                                </label>
                                <select
                                    id="class_id"
                                    name="class_id"
                                    class="select select-bordered w-full rounded-xl select-sm h-10 @error('class_id') select-error @enderror"
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

                            <div class="form-control">
                                <label class="label pb-1" for="excuse_date">
                                    <span class="label-text font-medium text-sm">Excuse Date <span class="text-error">*</span></span>
                                </label>
                                <input
                                    id="excuse_date"
                                    type="date"
                                    name="excuse_date"
                                    value="{{ old('excuse_date') }}"
                                    min="{{ now()->format('Y-m-d') }}"
                                    class="input input-bordered w-full rounded-xl input-sm h-10 @error('excuse_date') input-error @enderror"
                                    required
                                >
                                @error('excuse_date')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-control">
                                <label class="label pb-1" for="reason">
                                    <span class="label-text font-medium text-sm">Reason <span class="text-error">*</span></span>
                                </label>
                                <textarea
                                    id="reason"
                                    name="reason"
                                    rows="3"
                                    class="textarea textarea-bordered w-full rounded-xl text-sm @error('reason') textarea-error @enderror"
                                    placeholder="Briefly explain the reason for your absence..."
                                    maxlength="1000"
                                    required
                                >{{ old('reason') }}</textarea>
                                @error('reason')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-control">
                                <label class="label pb-1" for="document">
                                    <span class="label-text font-medium text-sm">Signed Letter (PDF) <span class="text-error">*</span></span>
                                </label>
                                <input
                                    id="document"
                                    type="file"
                                    name="document"
                                    accept=".pdf"
                                    class="file-input file-input-bordered w-full rounded-xl file-input-sm h-10 @error('document') file-input-error @enderror"
                                    required
                                >
                                <p class="mt-1.5 text-xs text-base-content/50">PDF only, max 5 MB. Upload a letter signed by your parent or guardian.</p>
                                @error('document')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end gap-3 pt-2">
                                <a href="{{ route('student.excuses.index') }}" class="btn btn-ghost rounded-xl btn-sm h-10">Cancel</a>
                                <button type="submit" class="btn btn-primary rounded-xl btn-sm h-10">Submit Request</button>
                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </main>
    </div>
</x-layouts.app>
