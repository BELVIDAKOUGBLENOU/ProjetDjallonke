<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo-dh.svg') }}" />
    {{-- for social media --}}
    <meta property="og:title" content="@yield('pageTitle', config('app.name', ''))" />
    <meta property="og:description" content="@yield('pageDescription', 'DJALLONKE')" />
    <meta property="og:image" content="{{ asset('logo-dh.svg') }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta name="twitter:card" content="summary_large_image" />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    {{-- cdn tabler-icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.26.0/dist/tabler-icons.min.css">
    <!-- Scripts -->
    @routes
    @if (!($disableDefaultAssets ?? false))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="theme-muted">
    <div id="app" class="flex h-screen overflow-hidden">
        <!-- Sidebar Overlay (Mobile) -->
        <div id="sidebar-overlay" class="fixed inset-0 z-40 backdrop-blur-sm transition-opacity lg:hidden"
            style="display: none;" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        @include('layouts.partials.sidebar')

        <!-- Main Content -->
        <div class="flex flex-1 flex-col overflow-hidden">
            @if (session('impersonate'))
                <div class="bg-red-600 text-white text-center px-4 py-2 text-sm font-medium shadow-md z-50">
                    Vous êtes connecté en tant que {{ auth()->user()->name }}.
                    <a href="{{ route('users.stop-impersonate') }}" class="underline ml-2 hover:text-red-100">Revenir à
                        mon
                        compte</a>
                </div>
            @endif
            <!-- Top Header -->
            @include('layouts.partials.topbar')



            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Success Message -->
                <div class="h-16"></div>
                @if (session('success'))
                    <div class="mb-6 flex items-center gap-3 rounded-lg theme-surface p-4">
                        <i class="ti ti-circle-check text-xl text-green-600"></i>
                        <span class="theme-body">{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Error Message -->
                @if (session('error'))
                    <div class="mb-6 flex items-center gap-3 rounded-lg theme-danger p-4">
                        <i class="ti ti-alert-circle text-xl text-red-700"></i>
                        <span class="text-red-800">{{ session('error') }}</span>
                    </div>
                @endif
                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-6 rounded-lg theme-danger p-4">
                        <div class="flex items-center gap-3 mb-2">
                            <i class="ti ti-alert-circle text-xl text-red-700"></i>
                            <span class="font-semibold text-red-800">Oops!</span>
                        </div>
                        <ul class="list-disc list-inside text-sm text-red-800 pl-8">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="">

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- JavaScript for Sidebar Toggle -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const isOpen = sidebar.style.transform === 'translateX(0px)';

            if (isOpen) {
                sidebar.style.transform = 'translateX(-100%)';
                overlay.style.display = 'none';
            } else {
                sidebar.style.transform = 'translateX(0px)';
                overlay.style.display = 'block';
            }
        }

        // Close sidebar on window resize if desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                document.getElementById('sidebar').style.transform = 'translateX(0px)';
                document.getElementById('sidebar-overlay').style.display = 'none';
            } else {
                document.getElementById('sidebar').style.transform = 'translateX(-100%)';
            }
        });

        // Initialize sidebar state on desktop
        if (window.innerWidth >= 1024) {
            document.getElementById('sidebar').style.transform = 'translateX(0px)';
        }

        // Dark Mode Toggle
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
        }

        // Initialize dark mode from localStorage
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }

        // Expose helpers globally for inline handlers
        window.__toggleSidebar = toggleSidebar;
        window.__toggleDarkMode = toggleDarkMode;
    </script>
    @include('layouts.partials.scripts')
    @stack('scripts')

</body>

</html>
