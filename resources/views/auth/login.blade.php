<x-layouts.auth title="Login">
    @if (session('status'))
        <x-alert>{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="grid gap-4">
        @csrf

        <x-form.field label="Email" name="email" type="email" required autofocus />
        <x-form.field label="Password" name="password" type="password" required />

        <label class="label cursor-pointer justify-start gap-3">
            <input type="checkbox" name="remember" class="checkbox checkbox-sm">
            <span class="label-text">Remember me</span>
        </label>

        <button type="submit" class="btn btn-primary rounded-md">Login</button>
    </form>

    <a href="{{ route('password.request') }}" class="link link-hover">Forgot password?</a>
</x-layouts.auth>
