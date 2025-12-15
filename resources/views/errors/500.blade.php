<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Erreur Serveur</title>

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
        <div class="absolute top-0 left-0 w-full h-1 bg-red-500"></div>

        <div
            class="mb-6 inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-50 dark:bg-red-900/20 text-red-500 dark:text-red-400">
            <i class="ti ti-server-off text-4xl"></i>
        </div>

        <h1 class="text-2xl font-bold theme-title mb-2">Erreur Serveur</h1>
        <p class="theme-muted-text mb-8 text-sm leading-relaxed">
            Oups, quelque chose s'est mal passé sur nos serveurs. Nous travaillons déjà à résoudre le problème.
        </p>

        <div class="flex flex-col gap-3">
            <a href="{{ route('home') }}"
                class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 rounded-lg bg-green-600 text-white hover:bg-green-700 transition font-medium text-sm shadow-sm shadow-green-200 dark:shadow-none">
                <i class="ti ti-home"></i>
                Retour à l'accueil
            </a>

            <button onclick="window.location.reload()"
                class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition font-medium text-sm">
                <i class="ti ti-refresh"></i>
                Réessayer
            </button>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800">
            <p class="text-xs text-gray-400 dark:text-gray-600 font-mono">Code d'erreur: 500 INTERNAL SERVER ERROR</p>
        </div>
    </div>

</body>

</html>
