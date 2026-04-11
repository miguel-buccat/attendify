<x-layouts.app title="Join a Class">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="classes" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8">
                <div class="max-w-md">

                    <div class="mb-6">
                        <a href="{{ route('student.classes.index') }}" class="inline-flex items-center gap-1.5 text-sm text-base-content/60 hover:text-base-content transition-colors mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                <path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Back to Classes
                        </a>
                        <h1 class="text-2xl md:text-3xl font-semibold">Join a Class</h1>
                        <p class="mt-1 text-base-content/60">Enter the 8-character invite code provided by your teacher.</p>
                    </div>

                    <form method="POST" action="{{ route('student.classes.join.store') }}" class="rounded-xl border border-base-300 bg-base-100 p-6 space-y-5">
                        @csrf

                        <div class="form-control">
                            <label class="label pb-1" for="invite_code">
                                <span class="label-text font-medium text-sm">Invite Code <span class="text-error">*</span></span>
                            </label>
                            <input
                                id="invite_code"
                                type="text"
                                name="invite_code"
                                value="{{ old('invite_code') }}"
                                class="input input-bordered w-full rounded-xl input-sm h-10 font-mono uppercase tracking-widest @error('invite_code') input-error @enderror"
                                placeholder="e.g. ABCD1234"
                                maxlength="8"
                                required
                                autofocus
                            >
                            @error('invite_code')
                                <p class="mt-1 text-xs text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="btn btn-primary rounded-xl">Join Class</button>
                        </div>
                    </form>

                </div>
            </div>
        </main>
    </div>
</x-layouts.app>
