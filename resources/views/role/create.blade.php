@extends('layouts.app')
@section('pageTitle', 'Créer un Rôle')

@section('content')
    <div class="active-nav-custom-url" url="{{ route('roles.index') }}"></div>

    <div class="">
        <div class="mb-6">
            <h1 class="text-lg font-semibold tracking-tight theme-title !mt-0">Nouveau Rôle</h1>
            <p class="text-sm theme-muted-text">Ajoutez un nouveau rôle au système.</p>
        </div>

        <div class="theme-surface border border-gray-200 rounded-xl shadow-sm p-6">
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom du Rôle</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2 px-3 border"
                            placeholder="Ex: Admin, Editeur..." required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('roles.index') }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Annuler
                        </a>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Créer le Rôle
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
