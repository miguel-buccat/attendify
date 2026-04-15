<x-layouts.app :title="$profileUser->name">
    <div class="flex min-h-screen bg-base-200">
        <x-nav.sidebar active="profile" />

        <main class="flex-1 min-w-0 lg:min-h-screen pt-14 lg:pt-0">

            {{-- Banner --}}
            <div class="d d1 relative h-40 md:h-52 bg-base-300 overflow-hidden">
                @if ($profileUser->banner_url)
                    <img src="{{ $profileUser->banner_url }}" alt="Profile banner" class="h-full w-full object-cover">
                @else
                    <div class="h-full w-full bg-gradient-to-br from-primary/20 via-secondary/10 to-accent/20"></div>
                @endif
            </div>

            <div class="px-4 md:px-8 pb-10">
                {{-- Avatar + actions row --}}
                <div class="d d2 flex items-end justify-between gap-4 -mt-10 md:-mt-12 mb-5 relative z-10">
                    <div class="shrink-0">
                        @if ($profileUser->avatar_url)
                            <img
                                src="{{ $profileUser->avatar_url }}"
                                alt="{{ $profileUser->name }}"
                                class="size-20 md:size-24 rounded-2xl object-cover border-4 border-base-200 shadow-md"
                            >
                        @else
                            <span class="inline-flex items-center justify-center size-20 md:size-24 rounded-2xl bg-primary/15 text-primary text-3xl font-black border-4 border-base-200 shadow-md">
                                {{ mb_strtoupper(mb_substr($profileUser->name, 0, 1)) }}
                            </span>
                        @endif
                    </div>
                    @if (auth()->id() === $profileUser->id)
                        <button type="button" onclick="document.getElementById('profile-modal').showModal(); setTimeout(showProfileEdit, 50)" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-base-200 text-base-content/60 border border-base-300/50 text-sm font-medium hover:bg-base-300/50 transition-colors mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="size-3.5" aria-hidden="true">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Edit Profile
                        </button>
                    @endif
                </div>

                {{-- Profile info --}}
                <div class="d d3 space-y-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-2xl font-black tracking-tight">{{ $profileUser->name }}</h1>
                            <x-ui.badge variant="primary" size="xs">{{ $profileUser->role->value }}</x-ui.badge>
                        </div>
                        <p class="text-sm text-base-content/50 mt-0.5">{{ $profileUser->email }}</p>
                    </div>

                    <p class="text-xs text-base-content/30">
                        Member since {{ $profileUser->created_at->format('F Y') }}
                    </p>
                </div>
            </div>
        </main>
    </div>

</x-layouts.app>
