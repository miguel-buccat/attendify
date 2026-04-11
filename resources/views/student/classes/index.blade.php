<x-layouts.app title="My Classes">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="classes" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">

                <div>
                    <h1 class="text-2xl md:text-3xl font-semibold">My Classes</h1>
                    <p class="mt-1 text-base-content/60">Classes you are enrolled in.</p>
                </div>

                @if (session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                @if ($classes->isEmpty())
                    <div class="rounded-xl border border-dashed border-base-300 bg-base-100 p-12 text-center">
                        <p class="text-base-content/50">You are not enrolled in any classes yet. Your teacher will add you to a class.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach ($classes as $class)
                            <a href="{{ route('student.classes.show', $class) }}" class="rounded-xl border border-base-300 bg-base-100 p-5 flex flex-col gap-3 hover:border-primary/30 hover:shadow-sm transition-all">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="font-semibold truncate">{{ $class->name }}</p>
                                        @if ($class->section)
                                            <p class="text-xs text-base-content/60 mt-0.5">{{ $class->section }}</p>
                                        @endif
                                    </div>
                                    @if ($class->status->value === 'Active')
                                        <span class="badge badge-success badge-sm shrink-0">Active</span>
                                    @else
                                        <span class="badge badge-ghost badge-sm shrink-0">Archived</span>
                                    @endif
                                </div>

                                @if ($class->description)
                                    <p class="text-sm text-base-content/60 line-clamp-2">{{ $class->description }}</p>
                                @endif

                                <div class="text-sm text-base-content/60 border-t border-base-200 pt-3 mt-auto">
                                    <span>{{ $class->students_count }} {{ Str::plural('student', $class->students_count) }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

            </div>
        </main>
    </div>
</x-layouts.app>
