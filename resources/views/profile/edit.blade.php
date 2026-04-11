<x-layouts.app title="Edit Profile">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="profile" />

        {{-- Main content --}}
        <main class="flex-1 min-w-0 pt-14 lg:pt-0">
            <div class="p-4 md:p-8">
                <div class="max-w-2xl">

                    <div class="mb-6">
                        <a href="{{ route('profile.show', auth()->user()) }}" class="inline-flex items-center gap-1.5 text-sm text-base-content/60 hover:text-base-content transition-colors mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-4" aria-hidden="true">
                                <path d="M19 12H5M12 5l-7 7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Back to Profile
                        </a>
                        <h1 class="text-2xl md:text-3xl font-semibold">Edit Profile</h1>
                        <p class="mt-1 text-base-content/60">Update your avatar, banner image, and bio.</p>
                    </div>

                    @if (session('success'))
                        <div class="rounded-xl border border-success/30 bg-success/5 p-4 mb-5">
                            <p class="text-sm text-success">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="rounded-xl border border-error/30 bg-error/5 p-4 mb-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <p class="text-sm text-error">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('PATCH')

                        {{-- Avatar --}}
                        <div class="rounded-xl border border-base-300 bg-base-100 p-5">
                            <h2 class="font-medium mb-4">Avatar</h2>
                            <div class="flex items-center gap-4">
                                <div class="shrink-0">
                                    @if (auth()->user()->avatar_url)
                                        <img id="avatar-preview" src="{{ auth()->user()->avatar_url }}" alt="Avatar preview" class="size-16 rounded-xl object-cover border border-base-300">
                                    @else
                                        <span id="avatar-placeholder" class="inline-flex items-center justify-center size-16 rounded-xl bg-primary/15 text-primary text-xl font-bold border border-base-300">
                                            {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                        </span>
                                        <img id="avatar-preview" src="" alt="Avatar preview" class="size-16 rounded-xl object-cover border border-base-300 hidden">
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <input
                                        type="file"
                                        id="avatar"
                                        name="avatar"
                                        accept="image/jpeg,image/png,image/webp"
                                        class="file-input file-input-bordered file-input-sm w-full rounded-xl @error('avatar') file-input-error @enderror"
                                        onchange="previewImage(this, 'avatar-preview', 'avatar-placeholder')"
                                    >
                                    <p class="text-xs text-base-content/50 mt-1.5">JPG, PNG or WebP · max 2 MB</p>
                                </div>
                            </div>
                        </div>

                        {{-- Banner --}}
                        <div class="rounded-xl border border-base-300 bg-base-100 p-5">
                            <h2 class="font-medium mb-4">Banner Image</h2>
                            <div class="space-y-3">
                                @if (auth()->user()->banner_url)
                                    <img id="banner-preview" src="{{ auth()->user()->banner_url }}" alt="Banner preview" class="h-24 w-full rounded-xl object-cover border border-base-300">
                                @else
                                    <div id="banner-placeholder" class="h-24 w-full rounded-xl bg-gradient-to-br from-primary/20 to-secondary/20 border border-base-300"></div>
                                    <img id="banner-preview" src="" alt="Banner preview" class="h-24 w-full rounded-xl object-cover border border-base-300 hidden">
                                @endif
                                <input
                                    type="file"
                                    id="banner"
                                    name="banner"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="file-input file-input-bordered file-input-sm w-full rounded-xl @error('banner') file-input-error @enderror"
                                    onchange="previewImage(this, 'banner-preview', 'banner-placeholder')"
                                >
                                <p class="text-xs text-base-content/50">JPG, PNG or WebP · max 4 MB</p>
                            </div>
                        </div>

                        {{-- About Me --}}
                        <div class="rounded-xl border border-base-300 bg-base-100 p-5">
                            <h2 class="font-medium mb-4">About Me</h2>
                            <div class="form-control">
                                <textarea
                                    id="about_me"
                                    name="about_me"
                                    rows="4"
                                    maxlength="1000"
                                    class="textarea textarea-bordered w-full rounded-xl resize-none @error('about_me') textarea-error @enderror"
                                    placeholder="Write a short bio about yourself…"
                                >{{ old('about_me', auth()->user()->about_me) }}</textarea>
                                <div class="flex justify-end mt-1">
                                    <span id="about-count" class="text-xs text-base-content/40">{{ mb_strlen(old('about_me', auth()->user()->about_me ?? '')) }}/1000</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="submit" class="btn btn-primary rounded-xl normal-case">Save Changes</button>
                            <a href="{{ route('profile.show', auth()->user()) }}" class="btn btn-ghost rounded-xl normal-case">Cancel</a>
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

        const textarea = document.getElementById('about_me');
        const counter = document.getElementById('about-count');
        textarea.addEventListener('input', () => {
            counter.textContent = textarea.value.length + '/1000';
        });
    </script>
</x-layouts.app>
