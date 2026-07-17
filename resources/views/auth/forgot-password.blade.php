<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }} - Forgot Password</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        @php $favicon = \App\Domains\Settings\Models\Setting::where('key', 'favicon')->value('value'); @endphp
        @if($favicon)
            <link rel="icon" type="image/x-icon" href="{{ \Illuminate\Support\Facades\Storage::url($favicon) }}">
        @else
            <link rel="icon" href="{{ asset('favicon.ico') }}">
        @endif
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @vite
        <style>body { font-family: 'Inter', sans-serif; }</style>
    </head>
    <body class="bg-gray-50 min-h-screen flex items-center justify-center p-8">
        <div class="w-full max-w-md">
            <div class="flex items-center mb-8 justify-center">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mr-3">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ config('app.name') }}</span>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Forgot your password?</h2>
                    <p class="text-gray-500 mt-1 text-sm">No worries. Enter your email and we'll send you a link to reset your password.</p>
                </div>
                @if(session('status'))
                    <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-700 text-center">{{ session('status') }}</div>
                @endif
                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none transition @error('email') border-red-300 @enderror">
                        @error('email') <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="w-full flex justify-center items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                        Send reset link
                    </button>
                </form>
                <p class="mt-6 text-center text-sm text-gray-500">
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500 transition">Back to sign in</a>
                </p>
            </div>
        </div>
    </body>
</html>
