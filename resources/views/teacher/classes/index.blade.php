<x-layouts.app title="My Classes">
    <style>
        @keyframes d-up { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        .d { animation: d-up .45s cubic-bezier(.16,1,.3,1) both; }
        .d1 { animation-delay: .00s; } .d2 { animation-delay: .07s; } .d3 { animation-delay: .14s; }
        .d4 { animation-delay: .21s; } .d5 { animation-delay: .28s; } .d6 { animation-delay: .35s; }
    </style>

    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="classes" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-6 lg:p-8 space-y-5">

                <div class="d d1 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Teacher</p>
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight">My Classes</h1>
                        <p class="mt-1 text-sm text-base-content/50">Manage your classes and enroll students.</p>
                    </div>
                    <a href="{{ route('teacher.classes.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-primary-content text-sm font-semibold hover:opacity-90 transition-opacity shrink-0 self-start sm:self-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        New Class
                    </a>
                </div>

                @if (session('success'))
                    <div class="d d2">
                        <x-alert type="success" :message="session('success')" />
                    </div>
                @endif

                @if ($classes->isEmpty())
                    <div class="d d2 rounded-2xl border-2 border-dashed border-base-300/60 bg-base-100 py-16 flex flex-col items-center gap-4 text-center px-6">
                        <div class="size-14 rounded-2xl bg-base-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-7 text-base-content/30"><path d="M4 6h16M4 10h16M4 14h8m-8 4h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-base-content/60">No classes yet</p>
                            <p class="text-sm text-base-content/40 mt-1">Create your first class to get started.</p>
                        </div>
                        <a href="{{ route('teacher.classes.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary/10 text-primary border border-primary/20 text-sm font-medium hover:bg-primary/15 transition-colors">Create a Class</a>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach ($classes as $i => $class)
                            @php $delay = 'd' . min($i + 2, 6); @endphp
                            <a href="{{ route('teacher.classes.show', $class) }}" class="d {{ $delay }} group rounded-2xl border border-base-300/50 bg-base-100 p-5 flex flex-col gap-4 hover:-translate-y-0.5 hover:shadow-lg hover:border-primary/20 transition-all">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="size-10 rounded-xl flex items-center justify-center bg-secondary/10 text-secondary font-black text-sm shrink-0">
                                        {{ mb_strtoupper(mb_substr($class->name, 0, 2)) }}
                                    </div>
                                    @if ($class->status->value === 'Active')
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold text-success bg-success/10 border-success/20">Active</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold text-base-content/50 bg-base-200 border-base-300/50">Archived</span>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-bold group-hover:text-primary transition-colors truncate">{{ $class->name }}</p>
                                    @if ($class->section)
                                        <p class="text-xs text-base-content/50 mt-0.5">{{ $class->section }}</p>
                                    @endif
                                    @if ($class->description)
                                        <p class="text-sm text-base-content/50 mt-2 line-clamp-2">{{ $class->description }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5 text-xs text-base-content/40 border-t border-base-300/30 pt-3 mt-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    {{ $class->students_count }} {{ Str::plural('student', $class->students_count) }}
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

            </div>
        </main>
    </div>
</x-layouts.app>
