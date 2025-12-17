@extends('auth.template')

@section('title', 'Inscription - ' . config('app.name'))
@section('subtitle', 'Créez votre compte gratuitement')
@section('header', 'Créer un compte')
@section('description', 'Remplissez le formulaire pour commencer')

@section('content')
    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium theme-body opacity-50">
                <i class="ti ti-user"></i> Nom complet
            </label>
            <div class="mt-1">
                <input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}"
                    class=" w-full " placeholder="John Doe">
            </div>
            @error('name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium theme-body opacity-50">
                <i class="ti ti-mail"></i> Adresse e-mail
            </label>
            <div class="mt-1">
                <input id="email" name="email" type="email" autocomplete="email" required
                    value="{{ old('email') }}" class=" w-full " placeholder="votre@email.com">
            </div>
            @error('email')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium theme-body opacity-50">
                <i class="ti ti-lock"></i> Mot de passe
            </label>
            <div class="mt-1 relative">
                <input id="password" name="password" type="password" autocomplete="new-password" required
                    class=" w-full pr-10 " placeholder="••••••••">
                <button type="button" onclick="togglePassword('password', this)"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none">
                    <i class="ti ti-eye"></i>
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium theme-body opacity-50">
                <i class="ti ti-lock-check"></i> Confirmer le mot de passe
            </label>
            <div class="mt-1 relative">
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                    required class=" w-full pr-10 " placeholder="••••••••">
                <button type="button" onclick="togglePassword('password_confirmation', this)"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none">
                    <i class="ti ti-eye"></i>
                </button>
            </div>
            @error('password_confirmation')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Terms and Conditions -->
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="terms" name="terms" type="checkbox" required
                    class="size-4 rounded border-gray-300 text-red-600 focus:ring-2 focus:ring-red-500/20 dark:border-gray-600 dark:bg-gray-700">
            </div>
            <div class="ml-3">
                <label for="terms" class="text-sm text-gray-600 dark:text-gray-400">
                    J'accepte les
                    <a href="#" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400">
                        conditions d'utilisation
                    </a>
                    et la
                    <a href="#" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400">
                        politique de confidentialité
                    </a>
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 py-3 font-semibold text-white shadow-sm transition hover:bg-primary-dark focus:outline-hidden focus:ring-2 focus:ring-primary/50 dark:bg-primary-dark dark:hover:bg-primary-darker">
                <i class="ti ti-user-plus"></i>
                <span>Créer mon compte</span>
            </button>
        </div>
    </form>
@endsection

@section('footer')
    <div class="flex items-center justify-center gap-1 text-sm">
        <span class="text-gray-600 dark:text-gray-400">Vous avez déjà un compte ?</span>
        <a href="{{ route('login') }}" class="font-medium text-primary-dark ">
            Se connecter
        </a>
    </div>
@endsection
