<x-layouts.app title="Notification Preferences">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="notifications" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-6 lg:p-8 space-y-5">

                <div>
                    <h1 class="text-2xl font-black tracking-tight">Notification Preferences</h1>
                    <p class="text-sm text-base-content/50 mt-1">Choose which notifications you'd like to receive</p>
                </div>

                @if (session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                <form method="POST" action="{{ route('student.notifications.update') }}" class="af-card !p-0 divide-y af-divider">
                    @csrf
                    @method('PATCH')

                    <div class="p-5 flex items-center justify-between gap-4">
                        <div>
                            <h3 class="font-semibold text-sm">Session Started</h3>
                            <p class="text-xs text-base-content/50 mt-0.5">Get notified when a teacher starts a class session</p>
                        </div>
                        <input type="hidden" name="session_started" value="0">
                        <input type="checkbox" name="session_started" value="1" class="toggle toggle-primary toggle-sm"
                            @checked($preferences['session_started'] ?? true) />
                    </div>

                    <div class="p-5 flex items-center justify-between gap-4">
                        <div>
                            <h3 class="font-semibold text-sm">Weekly Summary</h3>
                            <p class="text-xs text-base-content/50 mt-0.5">Receive a weekly email summary of your attendance</p>
                        </div>
                        <input type="hidden" name="weekly_summary" value="0">
                        <input type="checkbox" name="weekly_summary" value="1" class="toggle toggle-primary toggle-sm"
                            @checked($preferences['weekly_summary'] ?? true) />
                    </div>

                    <div class="p-5 flex items-center justify-between gap-4">
                        <div>
                            <h3 class="font-semibold text-sm">Absence Alerts</h3>
                            <p class="text-xs text-base-content/50 mt-0.5">Get notified when you're marked absent</p>
                        </div>
                        <input type="hidden" name="absence_alert" value="0">
                        <input type="checkbox" name="absence_alert" value="1" class="toggle toggle-primary toggle-sm"
                            @checked($preferences['absence_alert'] ?? true) />
                    </div>

                    <div class="p-5 flex items-center justify-between gap-4">
                        <div>
                            <h3 class="font-semibold text-sm">Excuse Request Updates</h3>
                            <p class="text-xs text-base-content/50 mt-0.5">Get notified when your excuse request is reviewed</p>
                        </div>
                        <input type="hidden" name="excuse_updates" value="0">
                        <input type="checkbox" name="excuse_updates" value="1" class="toggle toggle-primary toggle-sm"
                            @checked($preferences['excuse_updates'] ?? true) />
                    </div>

                    <div class="p-5">
                        <x-ui.button type="submit" variant="primary" size="sm">Save Preferences</x-ui.button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</x-layouts.app>
