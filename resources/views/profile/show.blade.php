<x-layouts.app :title="$profileUser->name">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="profile" />

        {{-- Main content --}}
        <main class="flex-1 min-w-0 pt-14 lg:pt-0">

            {{-- Banner --}}
            <div class="relative h-40 md:h-56 bg-base-300">
                <div class="absolute inset-0 overflow-hidden">
                    @if ($profileUser->banner_url)
                        <img src="{{ $profileUser->banner_url }}" alt="Profile banner" class="h-full w-full object-cover">
                    @else
                        <div class="h-full w-full bg-gradient-to-br from-primary/20 to-secondary/20"></div>
                    @endif
                </div>
            </div>

            <div class="px-4 md:px-8">
                {{-- Avatar + actions row --}}
                <div class="flex items-end justify-between gap-4 -mt-10 md:-mt-12 mb-4 relative z-10">
                    <div class="shrink-0">
                        @if ($profileUser->avatar_url)
                            <img
                                src="{{ $profileUser->avatar_url }}"
                                alt="{{ $profileUser->name }}"
                                class="size-20 md:size-24 rounded-2xl object-cover border-4 border-base-100 shadow-sm"
                            >
                        @else
                            <span class="inline-flex items-center justify-center size-20 md:size-24 rounded-2xl bg-primary/15 text-primary text-3xl font-bold border-4 border-base-100 shadow-sm">
                                {{ mb_strtoupper(mb_substr($profileUser->name, 0, 1)) }}
                            </span>
                        @endif
                    </div>
                    @if (auth()->id() === $profileUser->id)
                        <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline rounded-xl normal-case mb-2">
                            Edit Profile
                        </a>
                    @endif
                </div>

                {{-- Profile info --}}
                <div class="space-y-4 pb-8">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-2xl font-semibold">{{ $profileUser->name }}</h1>
                            <span class="badge badge-primary">{{ $profileUser->role->value }}</span>
                        </div>
                        <p class="text-sm text-base-content/60 mt-0.5">{{ $profileUser->email }}</p>
                    </div>

                    @if ($profileUser->about_me)
                        <div class="max-w-2xl">
                            <h2 class="text-sm font-semibold uppercase tracking-wider text-base-content/50 mb-2">About</h2>
                            <p class="text-base-content/80 leading-relaxed">{{ $profileUser->about_me }}</p>
                        </div>
                    @else
                        @if (auth()->id() === $profileUser->id)
                            <p class="text-sm text-base-content/50">
                                You haven't added an about me yet.
                                <a href="{{ route('profile.edit') }}" class="link link-primary">Add one</a>
                            </p>
                        @endif
                    @endif

                    <p class="text-xs text-base-content/40">
                        Member since {{ $profileUser->created_at->format('F Y') }}
                    </p>
                </div>
            </div>
        </main>
    </div>

</x-layouts.app>
