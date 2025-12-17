@extends('layouts.app')
@section('pageTitle', 'Détails du Rôle')

@section('content')
    <div class="active-nav-custom-url" url="{{ route('roles.index') }}"></div>

    <div class="">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-semibold tracking-tight theme-title !mt-0">Rôle : {{ $role->name }}</h1>
                <p class="text-sm theme-muted-text">Gérez les permissions associées à ce rôle.</p>
            </div>
            <a href="{{ route('roles.index') }}"
                class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                <span class="ti ti-arrow-left"></span>
                Retour
            </a>
        </div>

        <div class="theme-surface border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-sm font-semibold text-gray-900">Permissions du Rôle</h3>
                <span class="text-xs text-gray-500">Activez les permissions requises</span>
            </div>

            <form action="{{ route('roles.permissions.update', $role->id) }}" method="POST">
                @csrf
                <div class="p-6">
                    @if ($permissions->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            <span class="ti ti-lock-open-off text-2xl mb-2 block"></span>
                            Aucune permission définie dans le système.
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($permissions as $permission)
                                <div
                                    class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors">
                                    <span class="text-sm font-medium text-gray-700">{{ $permission->name }}</span>

                                    <label class="inline-flex items-center cursor-pointer relative">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                            class="sr-only peer"
                                            {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }} />
                                        <div
                                            class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600">
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-sm">
                        <span class="ti ti-device-floppy"></span>
                        Enregistrer les Permissions
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
