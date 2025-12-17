@extends('layouts.app')
@section('pageTitle', 'Liste des Rôles')

@section('content')
    <div class="mb-6 flex items-center justify-between">

        <div>
            <h1 class="text-lg font-semibold tracking-tight theme-title ">Rôles</h1>
            <p class="mb-2 text-sm theme-muted-text">Gérez les rôles et leurs permissions.</p>
        </div>
        <a href="{{ route('roles.create') }}"
            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium  rounded-md bg-secondary text-black focus:outline-none focus:ring-2">
            <span class="ti ti-plus "></span>
            Nouveau Rôle
        </a>
    </div>


    <div class="theme-surface theme-title border border-gray-200/70 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="border-b border-gray-200 text-[11px] uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="py-3 pl-4 pr-3 text-left font-medium">#</th>
                        <th class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nom
                        </th>
                        <th class="py-3 px-3 text-right font-medium w-px">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($roles as $role)
                        <tr class="group hover:bg-green-50/40 transition-colors">
                            <td class="whitespace-nowrap py-3 pl-4 pr-3 font-semibold text-gray-700">{{ ++$i }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $role->name }}</td>
                            <td class="whitespace-nowrap px-3 py-3 text-right">
                                <div
                                    class="flex items-center justify-end gap-1 opacity-70 group-hover:opacity-100 transition">
                                    <a href="{{ route('roles.show', $role->id) }}" title="Voir / Permissions"
                                        class="w-8 h-8 inline-flex items-center justify-center rounded-md text-slate-500 hover:text-green-600 hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-green-500/50 transition">
                                        <span class="ti ti-lock-access text-[17px]"></span>
                                    </a>
                                    <a href="{{ route('roles.edit', $role->id) }}" title="Modifier"
                                        class="w-8 h-8 inline-flex items-center justify-center rounded-md text-slate-500 hover:text-amber-600 hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-500/40 transition">
                                        <span class="ti ti-edit text-[17px]"></span>
                                    </a>
                                    @if (!in_array($role->name, ['Super-admin', 'Administrateur']))
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                            class="inline-flex"
                                            onsubmit="return confirmDeletion(event, 'Suppression', 'Voulez-vous vraiment supprimer ce rôle ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Supprimer"
                                                class="w-8 h-8 inline-flex items-center justify-center rounded-md text-slate-500 hover:text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500/40 transition">
                                                <span class="ti ti-trash text-[17px]"></span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="ti ti-database-off text-2xl text-gray-300"></span>
                                    <p class="font-medium">Aucun rôle trouvé.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-4 border-t border-gray-100">
            {!! $roles->links() !!}
        </div>
    </div>
@endsection
