<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Accès Refusé</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body class="theme-bg font-sans antialiased h-screen flex items-center justify-center p-4">

    <div
        class="theme-surface rounded-2xl shadow-xl p-8 max-w-md w-full text-center border border-gray-200/50 dark:border-gray-700/50 relative overflow-hidden">
        <!-- Decorative background element -->
        <div class="absolute top-0 left-0 w-full h-1 bg-secondary "></div>

        <div class="mb-6 inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-50 ">
            <i class="ti ti-lock-access-off text-4xl"></i>
        </div>

        <h1 class="text-2xl font-bold theme-title mb-2">Accès Refusé</h1>
        <p class="theme-muted-text mb-8 text-sm leading-relaxed">
            Désolé, vous n'avez pas les permissions nécessaires pour accéder à cette page. Si vous pensez qu'il s'agit
            d'une erreur, contactez l'administrateur.
        </p>

        <div class="flex flex-col gap-3">
            @if (url()->previous() !== url()->current() && url()->previous() !== route('login'))
                <a href="{{ url()->previous() }}"
                    class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition font-medium text-sm">
                    <i class="ti ti-arrow-left"></i>
                    Retour
                </a>
            @endif

            <a href="{{ route('home') }}"
                class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 rounded-lg bg-green-600 text-white hover:bg-green-700 transition font-medium text-sm shadow-sm shadow-green-200 dark:shadow-none">
                <i class="ti ti-home"></i>
                Retour à l'accueil
            </a>

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 rounded-lg border border-red-200 dark:border-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/10 transition font-medium text-sm">
                    <i class="ti ti-logout"></i>
                    Se déconnecter
                </button>
            </form>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800">
            <p class="text-xs text-gray-400 dark:text-gray-600 font-mono">Code d'erreur: 403 FORBIDDEN</p>
        </div>
    </div>

</body>

</html>
