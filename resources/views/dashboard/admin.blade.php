<x-layouts.app title="Admin Dashboard">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="dashboard" />

        {{-- Main content --}}
        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-semibold">Admin Dashboard</h1>
                        <p class="mt-1 text-base-content/60">Welcome back, {{ $user->name }}</p>
                    </div>
                    <span class="badge badge-primary badge-lg hidden lg:inline-flex">Admin</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    <article class="rounded-xl border border-base-300 bg-base-100 p-4">
                        <p class="text-xs uppercase tracking-wider text-base-content/60">Total Users</p>
                        <p class="mt-2 text-3xl font-bold">—</p>
                    </article>
                    <article class="rounded-xl border border-base-300 bg-base-100 p-4">
                        <p class="text-xs uppercase tracking-wider text-base-content/60">Active Classes</p>
                        <p class="mt-2 text-3xl font-bold">—</p>
                    </article>
                    <article class="rounded-xl border border-base-300 bg-base-100 p-4">
                        <p class="text-xs uppercase tracking-wider text-base-content/60">Sessions This Month</p>
                        <p class="mt-2 text-3xl font-bold">—</p>
                    </article>
                    <article class="rounded-xl border border-base-300 bg-base-100 p-4">
                        <p class="text-xs uppercase tracking-wider text-base-content/60">Avg Attendance Rate</p>
                        <p class="mt-2 text-3xl font-bold">—</p>
                    </article>
                </div>

                <div class="rounded-xl border border-dashed border-base-300 bg-base-100 p-8 text-center text-base-content/50">
                    Analytics charts coming in Phase 7
                </div>
            </div>
        </main>
    </div>

</x-layouts.app>
