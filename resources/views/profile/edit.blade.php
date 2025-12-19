@extends('layouts.app')

@section('title', 'Profil')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold theme-title">Paramètres du compte</h1>
            <p class="theme-muted-text">Gérez vos informations personnelles et la sécurité de votre compte.</p>
        </div>

        <!-- Profile Information -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

            <div class="rounded-xl border theme-divider theme-surface p-6 shadow-sm">
                <h2 class="text-lg font-semibold theme-title mb-4">Informations du profil</h2>
                <form action="{{ route('profile.update') }}" method="POST" class="space-y-4 max-w-xl">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="name" class="block text-sm font-medium theme-body mb-1">Nom</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                            class="w-full rounded-lg border theme-border bg-transparent px-4 py-2 theme-body focus:border-primary focus:ring-2 focus:ring-primary/20">
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium theme-body mb-1">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                            required
                            class="w-full rounded-lg border theme-border bg-transparent px-4 py-2 theme-body focus:border-primary focus:ring-2 focus:ring-primary/20">
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Update Password -->
            <div class="rounded-xl border theme-divider theme-surface p-6 shadow-sm">
                <h2 class="text-lg font-semibold theme-title mb-4">Mettre à jour le mot de passe</h2>
                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4 max-w-xl">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-sm font-medium theme-body mb-1">Mot de passe
                            actuel</label>
                        <input type="password" name="current_password" id="current_password" required
                            class="w-full rounded-lg border theme-border bg-transparent px-4 py-2 theme-body focus:border-primary focus:ring-2 focus:ring-primary/20">
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium theme-body mb-1">Nouveau mot de passe</label>
                        <input type="password" name="password" id="password" required
                            class="w-full rounded-lg border theme-border bg-transparent px-4 py-2 theme-body focus:border-primary focus:ring-2 focus:ring-primary/20">
                        @error('password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium theme-body mb-1">Confirmer le
                            mot de
                            passe</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full rounded-lg border theme-border bg-transparent px-4 py-2 theme-body focus:border-primary focus:ring-2 focus:ring-primary/20">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark transition">
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>

            <!-- Active Sessions -->
            <div class="col-span-2 rounded-xl border theme-divider theme-surface p-6 shadow-sm">
                <h2 class="text-lg font-semibold theme-title mb-4">Sessions actives</h2>
                <p class="text-sm theme-muted-text mb-4">Gérez et déconnectez vos sessions actives sur d'autres navigateurs
                    et
                    appareils.</p>

                <div class="space-y-4">
                    @foreach ($sessions as $session)
                        <div class="flex items-center justify-between p-3 rounded-lg border theme-divider">
                            <div class="flex items-center gap-3">
                                @if (str_contains($session->user_agent, 'Windows'))
                                    <i class="ti ti-brand-windows text-2xl theme-muted-text"></i>
                                @elseif (str_contains($session->user_agent, 'Mac'))
                                    <i class="ti ti-brand-apple text-2xl theme-muted-text"></i>
                                @elseif (str_contains($session->user_agent, 'Linux'))
                                    <i class="ti ti-brand-ubuntu text-2xl theme-muted-text"></i>
                                @elseif (str_contains($session->user_agent, 'Android'))
                                    <i class="ti ti-brand-android text-2xl theme-muted-text"></i>
                                @else
                                    <i class="ti ti-device-desktop text-2xl theme-muted-text"></i>
                                @endif

                                <div>
                                    <p class="text-sm font-medium theme-title">
                                        {{ $session->ip_address }}
                                        @if ($session->id === request()->session()->getId())
                                            <span class="ml-2 text-xs text-green-500 font-bold">(Cette session)</span>
                                        @endif
                                    </p>
                                    <p class="text-xs theme-muted-text truncate max-w-xs"
                                        title="{{ $session->user_agent }}">
                                        {{ $session->user_agent }}
                                    </p>
                                    <p class="text-xs theme-muted-text">
                                        Dernière activité :
                                        {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Delete Account -->
            <div class=" col-span-2 rounded-xl border   p-6 shadow-sm theme-surface theme-divider">
                <h2 class="text-lg font-semibold  mb-2">Supprimer le compte</h2>
                <p class="text-sm  mb-4">
                    Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées.
                </p>

                <form action="{{ route('profile.destroy') }}" method="POST"
                    onsubmit="return confirmDeletion(event, 'Supprimer le compte ?', 'Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible et toutes vos données seront perdues.')">
                    @csrf
                    @method('DELETE')

                    <div class="mb-4">
                        <label for="delete_password" class="block text-sm font-medium  mb-1">Mot
                            de passe pour confirmer</label>
                        <input type="password" name="password" id="delete_password" required
                            class="w-full max-w-xs rounded-lg border  bg-white px-4 py-2 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 dark:border-red-800 dark:bg-red-950 dark:text-red-100 dark:placeholder-red-700">
                        @error('password', 'userDeletion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition">
                        Supprimer mon compte
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
