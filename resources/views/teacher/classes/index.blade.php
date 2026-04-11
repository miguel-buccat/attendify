<x-layouts.app title="My Classes">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="classes" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-8 space-y-6">

                {{-- Header --}}
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-semibold">My Classes</h1>
                        <p class="mt-1 text-base-content/60">Manage your classes and enroll students.</p>
                    </div>
                    <a href="{{ route('teacher.classes.create') }}" class="btn btn-primary btn-sm rounded-xl gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        New Class
                    </a>
                </div>

                @if (session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                {{-- Class cards --}}
                @if ($classes->isEmpty())
                    <div class="rounded-xl border border-dashed border-base-300 bg-base-100 p-12 text-center">
                        <p class="text-base-content/50">No classes yet. Create your first class to get started.</p>
                        <a href="{{ route('teacher.classes.create') }}" class="btn btn-primary btn-sm rounded-xl mt-4">Create a Class</a>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach ($classes as $class)
                            <article class="rounded-xl border border-base-300 bg-base-100 p-5 flex flex-col gap-3">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <a href="{{ route('teacher.classes.show', $class) }}" class="font-semibold hover:text-primary transition-colors truncate block">
                                            {{ $class->name }}
                                        </a>
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

                                <div class="flex items-center justify-between text-sm text-base-content/60 border-t border-base-200 pt-3 mt-auto">
                                    <span>{{ $class->students_count }} {{ Str::plural('student', $class->students_count) }}</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif

            </div>
        </main>
    </div>
</x-layouts.app>
