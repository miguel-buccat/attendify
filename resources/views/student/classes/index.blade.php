<x-layouts.app title="My Classes">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="classes" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">

                <div class="d d1">
                    <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Student</p>
                    <h1 class="text-2xl md:text-3xl font-black tracking-tight">My Classes</h1>
                    <p class="mt-1 text-sm text-base-content/50">Classes you are enrolled in.</p>
                </div>

                @if (session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                @if ($classes->isEmpty())
                    <div class="d d2">
                        <x-ui.empty-state
                            icon="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"
                            title="No classes yet"
                            description="You are not enrolled in any classes yet. Your teacher will add you."
                        />
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach ($classes as $i => $class)
                            @php
                                $initials = collect(explode(' ', $class->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
                            @endphp
                            <a href="{{ route('student.classes.show', $class) }}" class="d d{{ min($i + 2, 6) }} group af-card p-5 flex flex-col gap-3 hover:-translate-y-0.5 hover:shadow-lg hover:border-primary/20 transition-all">
                                <div class="flex items-start gap-3">
                                    <div class="size-10 rounded-xl bg-secondary/10 text-secondary ring-1 ring-secondary/15 flex items-center justify-center text-sm font-black shrink-0">{{ $initials }}</div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold truncate">{{ $class->name }}</p>
                                        @if ($class->section)
                                            <p class="text-xs text-base-content/50 mt-0.5">{{ $class->section }}</p>
                                        @endif
                                    </div>
                                    @if ($class->isActive())
                                        <x-ui.badge variant="success" size="xs">{{ $class->status->value }}</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="neutral" size="xs">{{ $class->status->value }}</x-ui.badge>
                                    @endif
                                </div>

                                @if ($class->description)
                                    <p class="text-sm text-base-content/50 line-clamp-2">{{ $class->description }}</p>
                                @endif

                                <div class="text-xs text-base-content/40 border-t af-divider pt-3 mt-auto flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5" aria-hidden="true">
                                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
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
