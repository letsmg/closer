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
<body class="font-sans antialiased bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
    
    {{-- PUBLIC HEADER --}}
    <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">
        @if (Route::has('login'))
            <nav class="flex items-center justify-end gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                        {{ __('messages.login') }}
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                            {{ __('messages.register') }}
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    {{-- MAIN CONTENT --}}
    <main class="w-full">
        @yield('content')
    </main>

    {{-- PUBLIC FOOTER --}}
    <footer class="w-full lg:max-w-4xl max-w-[335px] text-sm mt-8 py-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
            <div class="text-[#706f6c] dark:text-[#A1A09A]">
                &copy; {{ date('Y') }} {{ config('app.name', 'Closer') }}. {{ __('messages.all_rights_reserved', ['year' => date('Y')]) }}
            </div>
            <nav class="flex items-center gap-4">
                <a href="{{ url('/terms') }}" class="text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] transition-colors">
                    {{ __('messages.terms_of_service') }}
                </a>
                <a href="{{ url('/privacy') }}" class="text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] transition-colors">
                    {{ __('messages.privacy_policy') }}
                </a>
                <a href="{{ url('/contact') }}" class="text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] transition-colors">
                    {{ __('messages.contact_us') }}
                </a>
            </nav>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
