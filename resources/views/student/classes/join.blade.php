<x-layouts.app title="Join a Class">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="classes" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-6 lg:p-8">
                <div class="max-w-md">

                    <div class="d d1 mb-6">
                        <a href="{{ route('student.classes.index') }}" class="inline-flex items-center gap-1.5 text-sm text-base-content/40 hover:text-base-content transition-colors mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                <path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Back to Classes
                        </a>
                        <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Student</p>
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight">Join a Class</h1>
                        <p class="mt-1 text-sm text-base-content/50">Enter the 8-character invite code provided by your teacher.</p>
                    </div>

                    <form method="POST" action="{{ route('student.classes.join.store') }}" class="d d2 af-card overflow-hidden !p-0">
                        @csrf
                        <div class="px-5 py-4 border-b af-divider">
                            <h2 class="font-semibold text-sm">Enter Invite Code</h2>
                        </div>
                        <div class="px-5 py-5 space-y-5">
                            <x-form.field name="invite_code" label="Invite Code" required>
                                <input
                                    id="invite_code"
                                    type="text"
                                    name="invite_code"
                                    value="{{ old('invite_code') }}"
                                    class="af-input font-mono uppercase tracking-widest @error('invite_code') af-input-error @enderror"
                                    placeholder="e.g. ABCD1234"
                                    maxlength="8"
                                    required
                                    autofocus
                                >
                            </x-form.field>

                            <div class="flex justify-end gap-3">
                                <x-ui.button type="submit" variant="primary">Join Class</x-ui.button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </main>
    </div>
</x-layouts.app>
