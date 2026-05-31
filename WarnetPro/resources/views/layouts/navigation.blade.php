<nav x-data="{ open: false }" class="bg-gray-900 border-b border-gray-800/50 sticky top-0 z-50 backdrop-blur-xl bg-gray-900/80">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-cyan-500/25">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-white">Warnet<span class="text-cyan-400">Pro</span></span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:items-center sm:ml-8 sm:space-x-1">
                    <a href="{{ route('dashboard') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-cyan-500/10 text-cyan-400' : 'text-gray-400 hover:text-gray-200 hover:bg-gray-800' }}">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            Dashboard
                        </span>
                    </a>
                    <a href="{{ route('computers.index') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('computers.*') ? 'bg-cyan-500/10 text-cyan-400' : 'text-gray-400 hover:text-gray-200 hover:bg-gray-800' }}">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Komputer
                        </span>
                    </a>
                    <a href="{{ route('bookings.index') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('bookings.*') ? 'bg-cyan-500/10 text-cyan-400' : 'text-gray-400 hover:text-gray-200 hover:bg-gray-800' }}">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Booking
                        </span>
                    </a>
                    <a href="{{ route('sessions.index') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('sessions.*') ? 'bg-cyan-500/10 text-cyan-400' : 'text-gray-400 hover:text-gray-200 hover:bg-gray-800' }}">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            History
                        </span>
                    </a>

                </div>
            </div>

            <!-- User Menu -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 transition-all duration-200">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-medium">{{ Auth::user()->name }}</div>
                                <div class="text-xs {{ Auth::user()->isAdmin() ? 'text-amber-400' : 'text-cyan-400' }}">{{ ucfirst(Auth::user()->role) }}</div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-gray-400 hover:text-gray-200 hover:bg-gray-800 transition duration-200">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-800">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-cyan-500/10 text-cyan-400' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">Dashboard</a>
            <a href="{{ route('computers.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('computers.*') ? 'bg-cyan-500/10 text-cyan-400' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">Komputer</a>
            <a href="{{ route('bookings.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('bookings.*') ? 'bg-cyan-500/10 text-cyan-400' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">Booking</a>
            <a href="{{ route('sessions.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium {{ request()->routeIs('sessions.*') ? 'bg-cyan-500/10 text-cyan-400' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">History</a>

        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-gray-800 px-4">
            <div class="flex items-center gap-3 px-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="font-medium text-white">{{ Auth::user()->name }}</div>
                    <div class="text-sm {{ Auth::user()->isAdmin() ? 'text-amber-400' : 'text-cyan-400' }}">{{ ucfirst(Auth::user()->role) }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-gray-400 hover:text-white hover:bg-gray-800">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                       class="block px-3 py-2 rounded-lg text-base font-medium text-gray-400 hover:text-white hover:bg-gray-800">Log Out</a>
                </form>
            </div>
        </div>
    </div>
</nav>
