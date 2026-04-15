<x-layouts.app title="Attendance Reports">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="reports" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-6 lg:p-8 space-y-5">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-black tracking-tight">Attendance Reports</h1>
                        <p class="text-sm text-base-content/50 mt-1">Generate system-wide attendance reports</p>
                    </div>
                </div>

                {{-- Date Range & Export --}}
                <div class="af-card p-5">
                    <h2 class="text-lg font-bold mb-4">Export Report</h2>
                    <form class="flex flex-wrap items-end gap-4">
                        <div>
                            <label class="label"><span class="label-text text-xs font-semibold">From</span></label>
                            <input type="date" name="from" value="{{ request('from', now()->subMonth()->format('Y-m-d')) }}"
                                class="af-input" />
                        </div>
                        <div>
                            <label class="label"><span class="label-text text-xs font-semibold">To</span></label>
                            <input type="date" name="to" value="{{ request('to', now()->format('Y-m-d')) }}"
                                class="af-input" />
                        </div>
                        <div class="flex gap-2">
                            <x-ui.button :href="route('admin.reports.export.csv', ['from' => request('from', now()->subMonth()->format('Y-m-d')), 'to' => request('to', now()->format('Y-m-d'))])" variant="outline" size="sm" id="csv-link">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke="currentColor" stroke-width="1.8"/><path d="M14 2v6h6" stroke="currentColor" stroke-width="1.8"/></svg>
                                CSV
                            </x-ui.button>
                            <x-ui.button :href="route('admin.reports.export.pdf', ['from' => request('from', now()->subMonth()->format('Y-m-d')), 'to' => request('to', now()->format('Y-m-d'))])" variant="primary" size="sm" id="pdf-link">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" stroke="currentColor" stroke-width="1.8"/><path d="M14 2v6h6" stroke="currentColor" stroke-width="1.8"/></svg>
                                PDF
                            </x-ui.button>
                        </div>
                    </form>
                </div>

                {{-- Class Attendance Overview --}}
                <div class="af-card overflow-hidden !p-0">
                    <div class="p-5 border-b af-divider">
                        <h2 class="text-lg font-bold">Class Attendance Overview</h2>
                        <p class="text-sm text-base-content/50 mt-0.5">Attendance rates across all active classes</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table table-sm">
                            <thead>
                                <tr class="border-b af-divider">
                                    <th class="text-xs font-bold uppercase tracking-wider text-base-content/40">#</th>
                                    <th class="text-xs font-bold uppercase tracking-wider text-base-content/40">Class</th>
                                    <th class="text-xs font-bold uppercase tracking-wider text-base-content/40">Teacher</th>
                                    <th class="text-xs font-bold uppercase tracking-wider text-base-content/40">Students</th>
                                    <th class="text-xs font-bold uppercase tracking-wider text-base-content/40">Sessions</th>
                                    <th class="text-xs font-bold uppercase tracking-wider text-base-content/40">Attendance Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($classesRanked as $index => $class)
                                    <tr class="border-b af-divider hover:bg-base-content/[.03]">
                                        <td class="text-sm text-base-content/50">{{ $index + 1 }}</td>
                                        <td class="font-semibold text-sm">{{ $class->name }}</td>
                                        <td class="text-sm text-base-content/70">{{ $class->teacher?->name ?? 'N/A' }}</td>
                                        <td class="text-sm text-base-content/70">{{ $class->student_count }}</td>
                                        <td class="text-sm text-base-content/70">{{ $class->total_sessions }}</td>
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <div class="w-24 bg-base-200 rounded-full h-2">
                                                    <div class="h-2 rounded-full {{ $class->attendance_rate >= 80 ? 'bg-success' : ($class->attendance_rate >= 60 ? 'bg-warning' : 'bg-error') }}"
                                                         style="width: {{ $class->attendance_rate }}%"></div>
                                                </div>
                                                <span class="text-sm font-bold {{ $class->attendance_rate >= 80 ? 'text-success' : ($class->attendance_rate >= 60 ? 'text-warning' : 'text-error') }}">
                                                    {{ $class->attendance_rate }}%
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-12 text-base-content/40">No active classes found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fromInput = document.querySelector('input[name="from"]');
            const toInput = document.querySelector('input[name="to"]');
            const csvLink = document.getElementById('csv-link');
            const pdfLink = document.getElementById('pdf-link');

            function updateLinks() {
                const from = fromInput.value;
                const to = toInput.value;
                csvLink.href = `{{ route('admin.reports.export.csv') }}?from=${from}&to=${to}`;
                pdfLink.href = `{{ route('admin.reports.export.pdf') }}?from=${from}&to=${to}`;
            }

            fromInput.addEventListener('change', updateLinks);
            toInput.addEventListener('change', updateLinks);
        });
    </script>
</x-layouts.app>
