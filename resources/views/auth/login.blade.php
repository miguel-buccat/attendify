<x-layouts.auth title="Login">
    @if (session('status'))
        <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
        @csrf

        <x-form.field label="Email" name="email" type="email" required autofocus />
        <x-form.field label="Password" name="password" type="password" required />

        <label class="flex items-center gap-3 cursor-pointer group">
            <input type="checkbox" name="remember" class="size-4 rounded-md border-base-300/70 text-primary focus:ring-primary/30 transition">
            <span class="text-sm text-base-content/60 group-hover:text-base-content/80 transition-colors">Remember me</span>
        </label>

        <x-ui.button type="submit" variant="primary" class="w-full">Login</x-ui.button>
    </form>

    <div class="text-center mt-2">
        <a href="{{ route('password.request') }}" class="text-sm text-primary/80 hover:text-primary transition-colors font-medium">Forgot password?</a>
    </div>
</x-layouts.auth>
