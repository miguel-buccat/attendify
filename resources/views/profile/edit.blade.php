<x-layouts.app title="Edit Profile">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="profile" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">
            <div class="p-4 md:p-6 lg:p-8">
                <div class="max-w-2xl">

                    <div class="d d1 mb-6">
                        <a href="{{ route('profile.show', auth()->user()) }}" class="inline-flex items-center gap-1.5 text-sm text-base-content/40 hover:text-base-content transition-colors mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                <path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Back to Profile
                        </a>
                        <p class="text-[11px] font-bold uppercase tracking-[.25em] text-base-content/35">Account</p>
                        <h1 class="text-2xl md:text-3xl font-black tracking-tight">Edit Profile</h1>
                        <p class="mt-1 text-sm text-base-content/50">Update your avatar and banner image.</p>
                    </div>

                    @if (session('success'))
                        <x-ui.alert type="success" class="d d2 mb-5">{{ session('success') }}</x-ui.alert>
                    @endif

                    @if ($errors->any())
                        <div class="d d2 rounded-2xl border border-error/30 bg-error/5 px-4 py-3 mb-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <p class="text-sm text-error">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        {{-- Avatar --}}
                        <div class="d d2 af-card !p-0 overflow-hidden">
                            <div class="px-5 py-4 border-b af-divider">
                                <h2 class="font-semibold text-sm">Avatar</h2>
                            </div>
                            <div class="px-5 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="shrink-0">
                                        @if (auth()->user()->avatar_url)
                                            <img id="avatar-preview" src="{{ auth()->user()->avatar_url }}" alt="Avatar preview" class="size-16 rounded-xl object-cover border border-base-300/50">
                                        @else
                                            <span id="avatar-placeholder" class="inline-flex items-center justify-center size-16 rounded-xl bg-primary/15 text-primary text-xl font-black border border-base-300/50">
                                                {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                            </span>
                                            <img id="avatar-preview" src="" alt="Avatar preview" class="size-16 rounded-xl object-cover border border-base-300/50 hidden">
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <input
                                            type="file"
                                            id="avatar"
                                            name="avatar"
                                            accept="image/jpeg,image/png,image/webp"
                                            class="af-input file:mr-3 file:border-0 file:bg-primary/10 file:text-primary file:text-xs file:font-semibold file:px-3 file:py-1 file:rounded-lg @error('avatar') af-input-error @enderror"
                                            onchange="previewImage(this, 'avatar-preview', 'avatar-placeholder')"
                                        >
                                        <p class="text-xs text-base-content/40 mt-1.5">JPG, PNG or WebP · max 2 MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Banner --}}
                        <div class="d d3 af-card !p-0 overflow-hidden">
                            <div class="px-5 py-4 border-b af-divider">
                                <h2 class="font-semibold text-sm">Banner Image</h2>
                            </div>
                            <div class="px-5 py-4 space-y-3">
                                @if (auth()->user()->banner_url)
                                    <img id="banner-preview" src="{{ auth()->user()->banner_url }}" alt="Banner preview" class="h-24 w-full rounded-xl object-cover border border-base-300/50">
                                @else
                                    <div id="banner-placeholder" class="h-24 w-full rounded-xl bg-gradient-to-br from-primary/20 via-secondary/10 to-accent/20 border border-base-300/50"></div>
                                    <img id="banner-preview" src="" alt="Banner preview" class="h-24 w-full rounded-xl object-cover border border-base-300/50 hidden">
                                @endif
                                <input
                                    type="file"
                                    id="banner"
                                    name="banner"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="af-input file:mr-3 file:border-0 file:bg-primary/10 file:text-primary file:text-xs file:font-semibold file:px-3 file:py-1 file:rounded-lg @error('banner') af-input-error @enderror"
                                    onchange="previewImage(this, 'banner-preview', 'banner-placeholder')"
                                >
                                <p class="text-xs text-base-content/40">JPG, PNG or WebP · max 4 MB</p>
                            </div>
                        </div>

                        <div class="d d4 flex items-center gap-3">
                            <x-ui.button type="submit" variant="primary">Save Changes</x-ui.button>
                            <x-ui.button variant="ghost" href="{{ route('profile.show', auth()->user()) }}">Cancel</x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        function previewImage(input, previewId, placeholderId) {
            const preview = document.getElementById(previewId);
            const placeholder = placeholderId ? document.getElementById(placeholderId) : null;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if (placeholder) { placeholder.classList.add('hidden'); }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-layouts.app>
