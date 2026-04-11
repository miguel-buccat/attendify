<x-layouts.auth title="Accept Invitation">
    <form method="POST" action="{{ route('invitation.accept.store', ['token' => $invitation->token]) }}" class="space-y-4">
        @csrf

        <div class="rounded-xl bg-base-200 px-4 py-3 text-sm space-y-1">
            <p class="text-base-content/60">You were invited to join as a</p>
            <p class="font-semibold text-base-content">{{ $invitation->role->value }}</p>
        </div>

        <div class="form-control">
            <label class="label" for="email">
                <span class="label-text font-medium">Email</span>
            </label>
            <input
                type="email"
                id="email"
                value="{{ $invitation->email }}"
                class="input input-bordered w-full rounded-xl bg-base-200"
                readonly
                disabled
            >
        </div>

        <div class="form-control">
            <label class="label" for="name">
                <span class="label-text font-medium">Full Name</span>
            </label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                class="input input-bordered w-full rounded-xl @error('name') input-error @enderror"
                placeholder="Your full name"
                required
                autofocus
            >
            @error('name')
                <div class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </div>
            @enderror
        </div>

        <div class="form-control">
            <label class="label" for="password">
                <span class="label-text font-medium">Password</span>
            </label>
            <input
                type="password"
                id="password"
                name="password"
                class="input input-bordered w-full rounded-xl @error('password') input-error @enderror"
                placeholder="Minimum 8 characters"
                required
            >
            @error('password')
                <div class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </div>
            @enderror
        </div>

        <div class="form-control">
            <label class="label" for="password_confirmation">
                <span class="label-text font-medium">Confirm Password</span>
            </label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="input input-bordered w-full rounded-xl"
                placeholder="Repeat your password"
                required
            >
        </div>

        <button type="submit" class="btn btn-primary w-full rounded-xl normal-case">
            Create Account
        </button>

        <p class="text-center text-xs text-base-content/50">
            Invitation expires {{ $invitation->expires_at->format('F j, Y') }}
        </p>
    </form>
</x-layouts.auth>
