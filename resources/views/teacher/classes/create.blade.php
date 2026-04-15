<x-layouts.app title="Create Class">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="classes" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-6 lg:p-8">
                <div class="max-w-xl">

                    <div class="d d1 mb-6">
                        <a href="{{ route('teacher.classes.index') }}" class="inline-flex items-center gap-1.5 text-sm text-base-content/40 hover:text-base-content transition-colors mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4"><path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            My Classes
                        </a>
                        <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Teacher</p>
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight">Create Class</h1>
                        <p class="mt-1 text-sm text-base-content/50">Set up a new class for your students.</p>
                    </div>

                    <form method="POST" action="{{ route('teacher.classes.store') }}" class="d d2 af-card p-6 space-y-5">
                        @csrf

                        <x-form.field name="name" label="Class Name" required>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" class="af-input @error('name') af-input-error @enderror" placeholder="e.g. ICT 101" required autofocus>
                        </x-form.field>

                        <x-form.field name="section" label="Section">
                            <input id="section" type="text" name="section" value="{{ old('section') }}" class="af-input @error('section') af-input-error @enderror" placeholder="e.g. Section A">
                        </x-form.field>

                        <x-form.field name="description" label="Description">
                            <textarea id="description" name="description" rows="3" class="af-input @error('description') af-input-error @enderror" placeholder="Optional class description">{{ old('description') }}</textarea>
                        </x-form.field>

                        <div class="flex justify-end pt-2">
                            <x-ui.button type="submit" variant="primary">Create Class</x-ui.button>
                        </div>
                    </form>

                </div>
            </div>
        </main>
    </div>
</x-layouts.app>
