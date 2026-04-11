<x-layouts.auth title="Invitation Invalid">
    <div class="space-y-4 text-center">
        <div class="rounded-xl bg-error/10 border border-error/30 px-4 py-4">
            @if ($reason === 'expired')
                <p class="text-error font-medium">This invitation has expired.</p>
                <p class="mt-1 text-sm text-base-content/60">Please ask an administrator to send you a new invitation.</p>
            @elseif ($reason === 'accepted')
                <p class="text-error font-medium">This invitation has already been used.</p>
                <p class="mt-1 text-sm text-base-content/60">If you already created an account, please log in.</p>
            @endif
        </div>

        <a href="{{ route('login') }}" class="btn btn-primary w-full rounded-xl normal-case">
            Go to Login
        </a>
    </div>
</x-layouts.auth>
