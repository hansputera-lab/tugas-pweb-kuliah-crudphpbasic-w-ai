<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'HRIS') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen" x-data="{ sidebarOpen: false }">
            {{-- Desktop Sidebar --}}
            <aside class="hidden md:flex md:flex-col md:fixed md:inset-y-0 md:w-64 bg-white border-r border-gray-200">
                <div class="flex items-center h-16 flex-shrink-0 px-6 border-b border-gray-200">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-indigo-600">
                        {{ config('app.name', 'HRIS') }}
                    </a>
                </div>
                <div class="flex-1 overflow-y-auto py-4 px-3 space-y-6">
                    @include('partials._sidebar_nav')
                </div>
            </aside>

            {{-- Mobile Sidebar (off-canvas) --}}
            <div x-show="sidebarOpen" class="md:hidden fixed inset-0 z-40" x-cloak>
                <div x-show="sidebarOpen" x-transition.opacity class="absolute inset-0 bg-gray-600 bg-opacity-50" @click="sidebarOpen = false"></div>
                <aside x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative flex flex-col w-64 max-w-xs h-full bg-white">
                    <div class="flex items-center justify-between h-16 flex-shrink-0 px-6 border-b border-gray-200">
                        <a href="{{ route('dashboard') }}" class="text-xl font-bold text-indigo-600">{{ config('app.name', 'HRIS') }}</a>
                        <button @click="sidebarOpen = false" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="flex-1 overflow-y-auto py-4 px-3 space-y-6">
                        @include('partials._sidebar_nav')
                    </div>
                </aside>
            </div>

            {{-- Main column --}}
            <div class="md:pl-64 flex flex-col min-h-screen">
                <header class="sticky top-0 z-30 flex items-center justify-between h-16 bg-white border-b border-gray-200 px-4 sm:px-6">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        @hasSection('header')
                            <h1 class="ml-2 md:ml-0 text-lg font-semibold text-gray-900">@yield('header')</h1>
                        @endif
                    </div>
                    <div class="flex items-center space-x-4">
                        @if(auth()->user()->canManageHR())
                            <span class="hidden sm:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ \App\Enums\Role::from(auth()->user()->role)->label() }}
                            </span>
                        @endif
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none">
                                <span class="mr-2">{{ Auth::user()->name }}</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5">
                                <div class="px-4 py-2 text-xs text-gray-400">{{ Auth::user()->email }}</div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1">
                    @yield('content')
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
