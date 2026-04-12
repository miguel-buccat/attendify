<x-layouts.app title="Class Overview">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="leaderboard" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-6 lg:p-8 space-y-5">

                <div>
                    <h1 class="text-2xl font-black tracking-tight">Class Overview</h1>
                    <p class="text-sm text-base-content/50 mt-1">Attendance rates across all active classes</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($classesRanked as $index => $class)
                        <div class="bg-base-100 border border-base-300/50 rounded-2xl p-5 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-base font-bold">{{ $class->name }}</h3>
                                    <p class="text-xs text-base-content/50 mt-0.5">{{ $class->teacher?->name ?? 'N/A' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-black {{ $class->attendance_rate >= 80 ? 'text-success' : ($class->attendance_rate >= 60 ? 'text-warning' : 'text-error') }}">
                                        {{ $class->attendance_rate }}%
                                    </p>
                                    <p class="text-[10px] text-base-content/40 uppercase font-bold tracking-wider">Rate</p>
                                </div>
                            </div>
                            <div class="mt-4 w-full bg-base-200 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all {{ $class->attendance_rate >= 80 ? 'bg-success' : ($class->attendance_rate >= 60 ? 'bg-warning' : 'bg-error') }}"
                                     style="width: {{ $class->attendance_rate }}%"></div>
                            </div>
                            <div class="mt-3 flex items-center gap-4 text-xs text-base-content/50">
                                <span>{{ $class->student_count }} students</span>
                                <span>{{ $class->total_sessions }} sessions</span>
                                <span>{{ $class->total_records }} records</span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-16 text-base-content/40">
                            No active classes found.
                        </div>
                    @endforelse
                </div>
            </div>
        </main>
    </div>
</x-layouts.app>
