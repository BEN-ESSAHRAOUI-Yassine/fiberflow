<div>
    {{-- Desktop sidebar --}}
    <aside
        class="fixed inset-y-0 left-0 z-40 w-64 flex flex-col bg-white border-r border-surface-200 shadow-surface
               lg:translate-x-0 transition-all duration-300 ease-in-out"
        :class="collapsed ? 'lg:w-[4.5rem]' : ''"
        x-bind:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        {{-- Logo --}}
        <div class="flex items-center h-14 px-4 border-b border-surface-200 shrink-0 gap-2.5">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 min-w-0">
                <x-application-logo class="w-8 h-8 shrink-0" />
                <span x-show="!collapsed" class="text-base font-semibold text-gray-900 whitespace-nowrap tracking-tight">FiberFlow</span>
            </a>
            <span x-show="!collapsed" class="hidden md:inline font-mono text-[9px] uppercase tracking-[0.2em] text-gray-400 border border-surface-200 rounded px-1.5 py-0.5">FTTH</span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6" aria-label="Main navigation">
            {{-- Overview --}}
            <div>
                <p x-show="!collapsed" class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('Overview') }}</p>
                <a href="{{ route('dashboard') }}"
                    title="{{ __('Dashboard') }}"
                    class="ff-nav-item @if (request()->routeIs('dashboard')) ff-nav-item-active @endif">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span x-show="!collapsed" class="whitespace-nowrap">{{ __('Dashboard') }}</span>
                </a>
            </div>

            {{-- Workspace --}}
            @can('viewAny', App\Models\Project::class)
                <div>
                    <p x-show="!collapsed" class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('Workspace') }}</p>
                    <a href="{{ route('admin.projects.index') }}"
                        title="{{ __('Projects') }}"
                        class="ff-nav-item @if (request()->routeIs('admin.projects.*')) ff-nav-item-active @endif">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
                        <span x-show="!collapsed" class="whitespace-nowrap">{{ __('Projects') }}</span>
                    </a>
                </div>
            @endcan

            {{-- Administration --}}
            @can('viewAny', App\Models\User::class)
                <div>
                    <p x-show="!collapsed" class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('Administration') }}</p>
                    <a href="{{ route('admin.users.index') }}"
                        title="{{ __('Users') }}"
                        class="ff-nav-item @if (request()->routeIs('admin.users.*')) ff-nav-item-active @endif">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/></svg>
                        <span x-show="!collapsed" class="whitespace-nowrap">{{ __('Users') }}</span>
                    </a>
                </div>
            @endcan
        </nav>

        {{-- User card --}}
        <div class="p-3 border-t border-surface-200 shrink-0">
            <div class="flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-surface-50 transition-colors">
                <div class="w-9 h-9 rounded-full bg-info-50 text-info-700 flex items-center justify-center text-sm font-semibold shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div x-show="!collapsed" class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->role?->value }}</p>
                </div>
            </div>
            <p x-show="!collapsed" class="mt-2 px-3 font-mono text-[9px] uppercase tracking-[0.18em] text-gray-300 select-none">
                ff-engine v2.1 &middot; build 8f2a
            </p>
        </div>
    </aside>

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden"></div>

    {{-- Topbar --}}
    <header class="sticky top-0 z-20 bg-white/80 backdrop-blur border-b border-surface-200">
        <div class="flex items-center gap-2 h-14 px-4 lg:px-6">
            <button @click="sidebarOpen = true" class="ff-icon-btn lg:hidden" aria-label="{{ __('Open navigation') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <button @click="toggleCollapse" class="hidden lg:inline-flex ff-icon-btn" aria-label="{{ __('Toggle sidebar') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!collapsed"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 5l-7 7 7 7M19 5l-7 7 7 7"/></svg>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="collapsed" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 19l7-7-7-7M5 19l7-7-7-7"/></svg>
            </button>

            <div class="flex-1"></div>

            {{-- Profile menu --}}
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center gap-2 pl-1.5 pr-2 py-1.5 rounded-lg hover:bg-surface-100 focus:outline-none transition-colors duration-150">
                        <div class="w-8 h-8 rounded-full bg-info-50 text-info-700 flex items-center justify-center text-sm font-semibold">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-medium text-gray-900">{{ Auth::user()->name }}</span>
                        <svg class="hidden sm:block h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="px-4 py-2 border-b border-surface-100">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                    </div>

                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-dropdown-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </header>
</div>
