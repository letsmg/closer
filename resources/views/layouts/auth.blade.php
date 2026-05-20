<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @rtl dir="rtl" @endrtl>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('app.name', 'Closer'))</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    
    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('head')
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen">
    
    {{-- AUTH HEADER --}}
    <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo / Brand --}}
                <div class="flex items-center">
                    <a href="{{ url('/dashboard') }}" class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ config('app.name', 'Closer') }}
                    </a>
                </div>

                {{-- Navigation --}}
                <nav class="flex items-center gap-2">
                    <a href="{{ url('/discover') }}" class="px-3 py-2 text-sm rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('messages.discover') }}
                    </a>
                    <a href="{{ url('/matches') }}" class="px-3 py-2 text-sm rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('messages.matches') }}
                    </a>
                    <a href="{{ url('/messages') }}" class="px-3 py-2 text-sm rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('messages.messages') }}
                    </a>
                    <a href="{{ url('/profile') }}" class="px-3 py-2 text-sm rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        {{ __('messages.profile') }}
                    </a>

                    {{-- Language Switcher --}}
                    <div class="ml-4 relative">
                        <select onchange="window.location.href=this.value"
                                class="text-sm bg-transparent border border-gray-300 dark:border-gray-600 rounded-md px-2 py-1">
                            <option value="{{ url('/lang/en') }}" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>EN</option>
                            <option value="{{ url('/lang/pt') }}" {{ app()->getLocale() == 'pt' ? 'selected' : '' }}>PT</option>
                            <option value="{{ url('/lang/ar') }}" {{ app()->getLocale() == 'ar' ? 'selected' : '' }}>AR</option>
                        </select>
                    </div>

                    {{-- User Menu --}}
                    <div class="ml-4">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-3 py-2 text-sm rounded-md text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                {{ __('messages.logout') }}
                            </button>
                        </form>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    {{-- AUTH FOOTER --}}
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400">
                <div>
                    &copy; {{ date('Y') }} {{ config('app.name', 'Closer') }}.
                </div>
                <nav class="flex items-center gap-4">
                    <a href="{{ url('/terms') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                        {{ __('messages.terms_of_service') }}
                    </a>
                    <a href="{{ url('/privacy') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                        {{ __('messages.privacy_policy') }}
                    </a>
                    <a href="{{ url('/help') }}" class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                        {{ __('messages.help') }}
                    </a>
                </nav>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
