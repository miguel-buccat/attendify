<x-layouts.app title="Join a Class">
    <style>
        @keyframes d-up { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        .d { animation: d-up .45s cubic-bezier(.16,1,.3,1) both; }
        .d1 { animation-delay: .00s; } .d2 { animation-delay: .07s; }
    </style>
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="classes" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8">
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

                    <form method="POST" action="{{ route('student.classes.join.store') }}" class="d d2 rounded-2xl border border-base-300/50 bg-base-100 overflow-hidden">
                        @csrf
                        <div class="px-5 py-4 border-b border-base-300/30">
                            <h2 class="font-semibold text-sm">Enter Invite Code</h2>
                        </div>
                        <div class="px-5 py-5 space-y-5">
                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="invite_code">
                                    Invite Code <span class="text-error">*</span>
                                </label>
                                <input
                                    id="invite_code"
                                    type="text"
                                    name="invite_code"
                                    value="{{ old('invite_code') }}"
                                    class="w-full rounded-xl border {{ $errors->has('invite_code') ? 'border-error' : 'border-base-300/70' }} bg-base-100 px-4 py-2.5 text-sm font-mono uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40"
                                    placeholder="e.g. ABCD1234"
                                    maxlength="8"
                                    required
                                    autofocus
                                >
                                @error('invite_code')
                                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end gap-3">
                                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-primary-content text-sm font-semibold hover:opacity-90 transition-opacity">
                                    Join Class
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </main>
    </div>
</x-layouts.app>
