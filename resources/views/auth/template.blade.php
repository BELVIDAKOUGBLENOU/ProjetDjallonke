<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.26.0/dist/tabler-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        const saved = localStorage.getItem('darkMode');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (saved === 'true' || (saved === null && prefersDark)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body class="min-h-screen theme-muted">
    <header class="flex items-center justify-between px-6 py-4 border-b border-gray-200 theme-surface theme-title">
        <!-- Left: Logo + App Name -->
        <a href="/" class="flex items-center gap-2  font-semibold">
            {{-- <i class="ti ti-brand-laravel text-red-500 text-2xl"></i> --}}
            <img src="{{ asset('logo-dh.svg') }}" style="max-height: 25px;" alt="">
            <span class="text-nowrap">{{ config('app.name', 'D-HARVEST') }}</span>
        </a>

        <!-- Right: Theme Toggle -->
        <button id="theme-toggle" type="button" onclick="window.__toggleDarkMode()"
            class="p-2 rounded-full  theme-body transition-colors focus:outline-none">
            <!-- Sun icon for dark mode (to switch to light) -->
            <i class="ti ti-sun text-xl hidden dark:block"></i>
            <!-- Moon icon for light mode (to switch to dark) -->
            <i class="ti ti-moon text-xl block dark:hidden"></i>
        </button>
    </header>

    <div class="max-w-7xl mx-auto px-4 my-4">
        <!-- Success Message -->
        @if (session('success'))
            <div class="mb-6 flex items-center gap-3 rounded-lg theme-surface p-4">
                <i class="ti ti-circle-check text-xl text-green-600"></i>
                <span class="text-sm theme-body">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Error Message -->
        @if (session('error'))
            <div class="mb-6 flex items-center gap-3 rounded-lg theme-danger p-4">
                <i class="ti ti-alert-circle text-xl text-red-700"></i>
                <span class="text-sm text-red-800">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="mb-6 rounded-lg theme-danger p-4">
                <div class="flex items-center gap-2 text-red-800">
                    <i class="ti ti-alert-triangle text-xl"></i>
                    <span class="font-semibold">Erreur de validation</span>
                </div>
                <ul class="mt-3 space-y-1 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li class="flex items-start gap-2">
                            <i class="ti ti-point-filled mt-1 text-xs"></i>
                            <span>{{ $error }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    <div class="flex  items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <!-- Logo / Brand -->





            <!-- Auth Card -->
            <div class="overflow-hidden rounded-2xl theme-surface shadow-xl">
                <div class="p-8">
                    <!-- Title -->
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold theme-title">
                            @yield('header')
                        </h2>
                        @hasSection('description')
                            <p class="mt-2 text-sm theme-body">
                                @yield('description')
                            </p>
                        @endif
                    </div>
                    <!-- Main Content -->
                    @yield('content')
                </div>

                <!-- Footer Links -->
                @hasSection('footer')
                    <div class="border-t theme-divider theme-muted px-8 py-4">
                        @yield('footer')
                    </div>
                @endif
            </div>

            <!-- Additional Links -->
            @hasSection('additional_links')
                <div class="mt-6 text-center text-sm theme-body">
                    @yield('additional_links')
                </div>
            @endif
        </div>
    </div>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ti-eye');
                icon.classList.add('ti-eye-off');
            } else {
                input.type = 'password';
                icon.classList.remove('ti-eye-off');
                icon.classList.add('ti-eye');
            }
        }
    </script>
</body>

</html>
