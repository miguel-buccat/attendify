<x-layouts.auth title="Accept Invitation">
    <form method="POST" action="{{ route('invitation.accept.store', ['token' => $invitation->token]) }}" class="space-y-4">
        @csrf

        <div class="flex items-center justify-between rounded-xl bg-base-200 px-4 py-3 text-sm">
            <p class="text-base-content/60 text-xs uppercase tracking-wide font-medium">Invited as</p>
            <x-ui.badge variant="primary" size="xs">{{ $invitation->role->value }}</x-ui.badge>
        </div>

        <div>
            <label class="text-[11px] font-bold uppercase tracking-[.2em] text-base-content/35 block mb-1.5" for="email">Email</label>
            <input
                type="email"
                id="email"
                value="{{ $invitation->email }}"
                class="af-input bg-base-200 text-base-content/60 cursor-not-allowed"
                readonly
                disabled
            >
        </div>

        <x-form.field name="name" label="Full Name" required>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $invitation->name) }}"
                class="af-input @error('name') af-input-error @enderror"
                placeholder="Your full name"
                required
                autofocus
            >
        </x-form.field>

        <x-form.field
            label="Password"
            name="password"
            type="password"
            required
            placeholder="Minimum 8 characters"
        />

        <x-form.field
            label="Confirm Password"
            name="password_confirmation"
            type="password"
            required
            placeholder="Repeat your password"
        />

        @if ($invitation->role === \App\Enums\UserRole::Student)
            <div class="rounded-2xl border border-base-300/50 bg-base-200/50 px-4 py-3 space-y-1">
                <p class="text-xs font-bold uppercase tracking-[.2em] text-base-content/40">Parent / Guardian Info</p>
                <p class="text-xs text-base-content/50">We'll notify them whenever you're marked absent.</p>
            </div>

            <x-form.field name="guardian_name" label="Parent/Guardian Full Name" required>
                <input
                    type="text"
                    id="guardian_name"
                    name="guardian_name"
                    value="{{ old('guardian_name') }}"
                    class="af-input @error('guardian_name') af-input-error @enderror"
                    placeholder="Full name"
                    required
                >
            </x-form.field>

            <x-form.field name="guardian_email" label="Parent/Guardian Email" required>
                <input
                    type="email"
                    id="guardian_email"
                    name="guardian_email"
                    value="{{ old('guardian_email') }}"
                    class="af-input @error('guardian_email') af-input-error @enderror"
                    placeholder="parent@example.com"
                    required
                >
            </x-form.field>
        @endif

        <x-ui.button type="submit" variant="primary" class="w-full mt-2">Create Account</x-ui.button>

        <p class="text-center text-xs text-base-content/40">
            Invitation expires {{ $invitation->expires_at->format('F j, Y') }}
        </p>
    </form>
</x-layouts.auth>
