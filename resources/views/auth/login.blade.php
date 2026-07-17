<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }} - Sign In</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        @php $favicon = \App\Domains\Settings\Models\Setting::where('key', 'favicon')->value('value'); @endphp
        @if($favicon)
            <link rel="icon" type="image/x-icon" href="{{ \Illuminate\Support\Facades\Storage::url($favicon) }}">
        @else
            <link rel="icon" href="{{ asset('favicon.ico') }}">
        @endif
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="bg-gray-50 min-h-screen">
        <div class="flex min-h-screen">
            {{-- Left Panel - Branding --}}
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 relative overflow-hidden">
                <div class="absolute inset-0">
                    <div class="absolute top-0 left-0 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 -translate-x-1/2 -translate-y-1/2"></div>
                    <div class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 translate-x-1/2 translate-y-1/2"></div>
                </div>
                <div class="relative z-10 flex flex-col justify-center px-16 text-white">
                    <div class="mb-8">
                        <div class="flex items-center mb-6">
                            <div class="flex items-center justify-center mr-4">
                                <x-company-logo size="lg" class="h-10 w-auto brightness-0 invert" />
                            </div>
                            <span class="text-2xl font-bold tracking-tight">{{ config('app.name') }}</span>
                        </div>
                        <h1 class="text-4xl font-bold leading-tight mb-4">
                            Human Resource<br>Information System
                        </h1>
                        <p class="text-blue-100 text-lg leading-relaxed max-w-md">
                            Manage your workforce efficiently with our comprehensive HR platform. Track attendance, manage leaves, and gain insights from powerful analytics.
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-4 max-w-md mt-8">
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                            <div class="text-2xl font-bold">100%</div>
                            <div class="text-blue-200 text-sm">Digital HR</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                            <div class="text-2xl font-bold">24/7</div>
                            <div class="text-blue-200 text-sm">Access Anytime</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                            <div class="text-2xl font-bold">Real-time</div>
                            <div class="text-blue-200 text-sm">Analytics</div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                            <div class="text-2xl font-bold">Secure</div>
                            <div class="text-blue-200 text-sm">Data Protection</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Panel - Login Form --}}
            <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
                <div class="w-full max-w-md">
                    {{-- Mobile Logo --}}
                    <div class="lg:hidden flex items-center mb-8">
                        <x-company-logo class="h-9 w-auto mr-3" />
                        <span class="text-xl font-bold text-gray-900">{{ config('app.name') }}</span>
                    </div>

                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-1">Sign in to your account</h2>
                        <p class="text-gray-500 mb-8">Welcome back! Please enter your credentials.</p>
                    </div>

                    @if(session('status'))
                        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="you@company.com"
                                class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition duration-150 @error('email') border-red-300 focus:border-red-500 focus:ring-red-500/20 @enderror"
                            >
                            @error('email')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                            <div x-data="{ show: false }" class="relative">
                                <input
                                    id="password"
                                    :type="show ? 'text' : 'password'"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Enter your password"
                                    class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 pr-12 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition duration-150 @error('password') border-red-300 focus:border-red-500 focus:ring-red-500/20 @enderror"
                                >
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                    <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="show" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-600">Remember me</span>
                            </label>
                            @if(Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500 transition">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <button type="submit" class="w-full flex justify-center items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-sm rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150">
                            Sign in
                        </button>
                    </form>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <p class="text-xs text-gray-500 text-center">
                            Demo accounts: admin@hris.test, hr@hris.test, employee@hris.test (password: password)
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
