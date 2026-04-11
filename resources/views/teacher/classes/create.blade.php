<x-layouts.app title="Create Class">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="classes" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8">
                <div class="max-w-2xl">

                    <div class="mb-6">
                        <a href="{{ route('teacher.classes.index') }}" class="inline-flex items-center gap-1.5 text-sm text-base-content/60 hover:text-base-content transition-colors mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                <path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Back to Classes
                        </a>
                        <h1 class="text-2xl md:text-3xl font-semibold">Create Class</h1>
                        <p class="mt-1 text-base-content/60">Set up a new class for your students.</p>
                    </div>

                    <form method="POST" action="{{ route('teacher.classes.store') }}" class="rounded-xl border border-base-300 bg-base-100 p-6 space-y-5">
                        @csrf

                        <div class="form-control">
                            <label class="label pb-1" for="name">
                                <span class="label-text font-medium text-sm">Class Name <span class="text-error">*</span></span>
                            </label>
                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                class="input input-bordered w-full rounded-xl input-sm h-10 @error('name') input-error @enderror"
                                placeholder="e.g. ICT 101"
                                required
                                autofocus
                            >
                            @error('name')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label pb-1" for="section">
                                <span class="label-text font-medium text-sm">Section</span>
                            </label>
                            <input
                                id="section"
                                type="text"
                                name="section"
                                value="{{ old('section') }}"
                                class="input input-bordered w-full rounded-xl input-sm h-10 @error('section') input-error @enderror"
                                placeholder="e.g. Section A"
                            >
                            @error('section')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label pb-1" for="description">
                                <span class="label-text font-medium text-sm">Description</span>
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="3"
                                class="textarea textarea-bordered w-full rounded-xl @error('description') textarea-error @enderror"
                                placeholder="Optional class description"
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="btn btn-primary rounded-xl">Create Class</button>
                        </div>
                    </form>

                </div>
            </div>
        </main>
    </div>
</x-layouts.app>
