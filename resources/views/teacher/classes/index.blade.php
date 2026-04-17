<x-layouts.app title="My Classes">
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
                    <x-ui.button variant="primary" class="shrink-0 self-start sm:self-auto" onclick="document.getElementById('create-class-modal').showModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                        New Class
                    </x-ui.button>
                </div>

                @if (session('success'))
                    <div class="d d2">
                        <x-alert type="success" :message="session('success')" />
                    </div>
                @endif

                @if ($classes->isEmpty())
                    <div class="d d2">
                        <x-ui.empty-state
                            icon="M4 6h16M4 10h16M4 14h8m-8 4h5"
                            title="No classes yet"
                            description="Create your first class to get started."
                        >
                            <x-ui.button variant="outline" size="sm" onclick="document.getElementById('create-class-modal').showModal()">Create a Class</x-ui.button>
                        </x-ui.empty-state>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach ($classes as $i => $class)
                            @php $delay = 'd' . min($i + 2, 6); @endphp
                            <a href="{{ route('teacher.classes.show', $class) }}" class="d {{ $delay }} group af-card p-5 flex flex-col gap-4 hover:-translate-y-0.5 hover:shadow-lg hover:border-primary/20 transition-all">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="size-10 rounded-xl flex items-center justify-center bg-secondary/10 text-secondary ring-1 ring-secondary/15 font-black text-sm shrink-0">
                                        {{ mb_strtoupper(mb_substr($class->name, 0, 2)) }}
                                    </div>
                                    @if ($class->status->value === 'Active')
                                        <x-ui.badge variant="success">Active</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="neutral">Archived</x-ui.badge>
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
                                <div class="flex items-center gap-1.5 text-xs text-base-content/40 border-t af-divider pt-3 mt-auto">
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

    {{-- Create Class Modal --}}
    <dialog id="create-class-modal" class="modal">
        <div class="af-modal-box modal-box rounded-2xl border border-base-300/30 shadow-2xl max-w-md">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-bold tracking-tight">Create New Class</h3>
                <form method="dialog">
                    <button class="af-btn af-btn-ghost af-btn-icon af-btn-sm rounded-xl text-base-content/40 hover:text-base-content/70">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                </form>
            </div>

            @if ($errors->any())
                <x-ui.alert variant="error" class="mb-4">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('teacher.classes.store') }}" class="space-y-4">
                @csrf

                <x-form.field name="name" label="Class Name" required>
                    <input id="create-class-name" type="text" name="name" value="{{ old('name') }}" class="af-input @error('name') af-input-error @enderror" placeholder="e.g. ICT 101" required autofocus>
                </x-form.field>

                <x-form.field name="section" label="Section">
                    <input type="text" name="section" value="{{ old('section') }}" class="af-input @error('section') af-input-error @enderror" placeholder="e.g. Section A">
                </x-form.field>

                <x-form.field name="description" label="Description">
                    <textarea name="description" rows="3" class="af-input @error('description') af-input-error @enderror" placeholder="Optional class description">{{ old('description') }}</textarea>
                </x-form.field>

                <div class="flex justify-end gap-2 pt-1">
                    <x-ui.button type="button" variant="ghost" onclick="this.closest('dialog').close()">Cancel</x-ui.button>
                    <x-ui.button type="submit" variant="primary">Create Class</x-ui.button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    @if ($errors->any())
        <script>document.getElementById('create-class-modal')?.showModal();</script>
    @endif
</x-layouts.app>
