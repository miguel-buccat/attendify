<x-layouts.app title="Mark Attendance">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-6">
                <img src="/assets/attendify.png" alt="Attendify" class="size-12 mx-auto mb-3">
                <h1 class="text-2xl font-black tracking-tight">Mark Attendance</h1>
                @if ($session)
                    <p class="text-sm text-base-content/50 mt-1">{{ $session->schoolClass->name }}</p>
                    <p class="text-xs text-base-content/40">{{ $session->start_time->format('M d, Y — g:i A') }}</p>
                @endif
            </div>

            <div class="bg-base-100 rounded-2xl border border-base-300/50 p-6">
                @if (session('attendance_success'))
                    <div class="text-center space-y-3">
                        <div class="size-16 rounded-full bg-success/10 flex items-center justify-center mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-8 text-success"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <p class="text-lg font-bold text-success">{{ session('attendance_success') }}</p>
                        <p class="text-sm text-base-content/50">You may close this page.</p>
                    </div>
                @elseif ($error)
                    <div class="text-center space-y-3">
                        <div class="size-16 rounded-full bg-error/10 flex items-center justify-center mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-8 text-error"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <p class="text-lg font-bold text-error">{{ $error }}</p>
                    </div>
                @else
                    <p class="text-sm text-base-content/60 mb-4">Enter your email address to record your attendance.</p>

                    <form method="POST" action="{{ route('attend.store', [$session->id, $token]) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="email" class="block text-sm font-medium mb-1.5">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                                class="input input-bordered w-full rounded-xl @error('email') input-error @enderror"
                                placeholder="your@email.com">
                            @error('email')
                                <p class="text-error text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-full rounded-xl">Record Attendance</button>
                    </form>
                @endif
            </div>

            <p class="text-center text-xs text-base-content/30 mt-4">Powered by Attendify</p>
        </div>
    </div>
</x-layouts.app>
