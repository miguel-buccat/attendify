<x-layouts.auth title="Reset Password">
    <form method="POST" action="{{ route('password.update') }}" class="grid gap-4">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-form.field label="Email" name="email" type="email" required autofocus :value="$request->email" />
        <x-form.field label="New Password" name="password" type="password" required />
        <x-form.field label="Confirm Password" name="password_confirmation" type="password" required />

        <button type="submit" class="btn btn-primary rounded-md">Reset Password</button>
    </form>
</x-layouts.auth>
