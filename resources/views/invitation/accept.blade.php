<x-layouts.auth title="Accept Invitation">
    <form method="POST" action="{{ route('invitation.accept.store', ['token' => $invitation->token]) }}" class="space-y-4">
        @csrf

        <div class="flex items-center justify-between rounded-xl bg-base-200 px-4 py-3 text-sm">
            <p class="text-base-content/60 text-xs uppercase tracking-wide font-medium">Invited as</p>
            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold text-primary bg-primary/10 border-primary/20">{{ $invitation->role->value }}</span>
        </div>

        <div>
            <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="email">Email</label>
            <input
                type="email"
                id="email"
                value="{{ $invitation->email }}"
                class="w-full rounded-xl border border-base-300/70 bg-base-200 px-3 py-2.5 text-sm text-base-content/60 cursor-not-allowed"
                readonly
                disabled
            >
        </div>

        <div>
            <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="name">Full Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $invitation->name) }}"
                class="w-full rounded-xl border {{ $errors->has('name') ? 'border-error' : 'border-base-300/70' }} bg-base-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40"
                placeholder="Your full name"
                required
                autofocus
            >
            @error('name')
                <p class="mt-1 text-xs text-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                class="w-full rounded-xl border {{ $errors->has('password') ? 'border-error' : 'border-base-300/70' }} bg-base-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40"
                placeholder="Minimum 8 characters"
                required
            >
            @error('password')
                <p class="mt-1 text-xs text-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="password_confirmation">Confirm Password</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="w-full rounded-xl border border-base-300/70 bg-base-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40"
                placeholder="Repeat your password"
                required
            >
        </div>

        @if ($invitation->role === \App\Enums\UserRole::Student)
            <div class="rounded-2xl border border-base-300/50 bg-base-200/50 px-4 py-3 space-y-1">
                <p class="text-xs font-bold uppercase tracking-[.2em] text-base-content/40">Parent / Guardian Info</p>
                <p class="text-xs text-base-content/50">We'll notify them whenever you're marked absent.</p>
            </div>

            <div>
                <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="guardian_email">Parent/Guardian Email</label>
                <input
                    type="email"
                    id="guardian_email"
                    name="guardian_email"
                    value="{{ old('guardian_email') }}"
                    class="w-full rounded-xl border {{ $errors->has('guardian_email') ? 'border-error' : 'border-base-300/70' }} bg-base-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40"
                    placeholder="parent@example.com"
                    required
                >
                @error('guardian_email')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="guardian_phone">Parent/Guardian Phone</label>
                <input
                    type="tel"
                    id="guardian_phone"
                    name="guardian_phone"
                    value="{{ old('guardian_phone') }}"
                    class="w-full rounded-xl border {{ $errors->has('guardian_phone') ? 'border-error' : 'border-base-300/70' }} bg-base-100 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40"
                    placeholder="+63 000 000 0000"
                    required
                >
                @error('guardian_phone')<p class="mt-1 text-xs text-error">{{ $message }}</p>@enderror
            </div>
        @endif

        <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-primary-content text-sm font-semibold hover:opacity-90 transition-opacity">
            Create Account
        </button>

        <p class="text-center text-xs text-base-content/40">
            Invitation expires {{ $invitation->expires_at->format('F j, Y') }}
        </p>
    </form>
</x-layouts.auth>
