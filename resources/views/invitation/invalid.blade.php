<x-layouts.auth title="Invitation Invalid">
    <div class="space-y-4 text-center">
        <div class="rounded-2xl bg-error/10 border border-error/20 px-5 py-5 space-y-1">
            <div class="size-12 rounded-xl bg-error/10 flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-6 text-error" aria-hidden="true">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            @if ($reason === 'expired')
                <p class="text-error font-semibold">This invitation has expired.</p>
                <p class="text-sm text-base-content/60">Please ask an administrator to send you a new invitation.</p>
            @elseif ($reason === 'accepted')
                <p class="text-error font-semibold">This invitation has already been used.</p>
                <p class="text-sm text-base-content/60">If you already created an account, please log in.</p>
            @endif
        </div>

        <a href="{{ route('login') }}" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-primary-content text-sm font-semibold hover:opacity-90 transition-opacity">
            Go to Login
        </a>
    </div>
</x-layouts.auth>
