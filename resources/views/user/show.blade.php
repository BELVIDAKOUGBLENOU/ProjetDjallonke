@extends('layouts.app-d')
@section('pageTitle', 'Détails - Utilisateur')
@section('content')
    <div class="active-nav-custom-url" url="{{ route('users.index') }}"></div>

    <div class="">
        {{-- Header / Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight theme-title">Détails de l'utilisateur</h1>
                <p class="mt-0 text-sm theme-muted-text">Détails et informations de l'utilisateur.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 justify-end">
                <a href="{{ route('users.index') }}"
                    class="flex items-center gap-2 rounded-md border border-gray-300 theme-surface theme-title px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <span class="ti ti-arrow-left text-base"></span>
                    Retour
                </a>
                <a href="{{ route('users.edit', $user->id) }}"
                    class="flex items-center gap-2 rounded-md border border-gray-300 theme-surface theme-title px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <span class="ti ti-edit text-base"></span>
                    Modifier
                </a>
                <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                    onsubmit="return confirmDeletion(event, 'Suppresion', 'Voulez-vous vraiment supprimer cette donnée, Cette action est irréversible ?')"
                    class="inline-flex">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="flex items-center gap-2 rounded-md border border-red-200 theme-surface theme-title px-4 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500/40">
                        <span class="ti ti-trash text-base"></span>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>

        {{-- Details Card --}}
        <div class="theme-surface theme-title border border-gray-200/70 rounded-xl shadow-sm px-6 py-2 mt-5">
            <dl class="divide-y theme-divider">

                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Nom</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $user->name }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Email</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $user->email }}</dd>
                </div>

                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Rôles</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm">
                        @forelse($user->roles as $role)
                            <span
                                class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">{{ $role->name }}</span>
                        @empty
                            <span class="text-gray-500 italic">Aucun rôle assigné</span>
                        @endforelse
                    </dd>
                </div>

                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Permissions directes
                    </dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm">
                        @forelse($user->getDirectPermissions() as $permission)
                            <span
                                class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">{{ $permission->name }}</span>
                        @empty
                            <span class="text-gray-500 italic">Aucune permission directe</span>
                        @endforelse
                    </dd>
                </div>

            </dl>
        </div>
    </div>
@endsection
