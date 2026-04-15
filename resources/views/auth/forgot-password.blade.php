<x-layouts.auth title="Forgot Password">
    <p class="text-sm text-base-content/70">Enter your email and we will send a password reset link.</p>

    @if (session('status'))
        <x-alert>{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="grid gap-4">
        @csrf

        <x-form.field label="Email" name="email" type="email" required autofocus />

        <x-ui.button type="submit" variant="primary" class="w-full">Send Reset Link</x-ui.button>
    </form>

    <a href="{{ route('login') }}" class="link link-hover">Back to login</a>
</x-layouts.auth>
