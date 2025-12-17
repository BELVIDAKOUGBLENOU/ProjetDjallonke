@extends('layouts.app-d')
@section('pageTitle', 'Details -  District ')
@section('content')
    <div class="active-nav-custom-url" url="{{ route('districts.index') }}"></div>

    <div class="">
        {{-- Header / Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight theme-title">Détails District</h1>
                <p class="mt-0 text-sm .theme-muted-text">Détails et informations de la District.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 justify-end">
                <a href="{{ route('districts.index') }}" class="flex items-center gap-2 rounded-md border border-gray-300 theme-surface theme-title px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <span class="ti ti-arrow-left text-base"></span>
                    Retour
                </a>
                {{-- @can('update ' . \App\Models\District::getTableName()) --}}

                <a href="{{ route('districts.edit', $district->id) }}" class="flex items-center gap-2 rounded-md border border-gray-300 theme-surface theme-title px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <span class="ti ti-edit text-base"></span>
                    Modifier
                </a>
                 {{-- @endcan --}}

                {{-- @can('delete ' . \App\Models\District::getTableName()) --}}

                <form action="{{ route('districts.destroy', $district->id) }}" method="POST" onsubmit="return confirmDeletion(event, 'Suppresion', 'Voulez-vous vraiment supprimer cette donnée, Cette action est irréversible ?')" class="inline-flex">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center gap-2 rounded-md border border-red-200 theme-surface theme-title px-4 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500/40">
                        <span class="ti ti-trash text-base"></span>
                        Supprimer
                    </button>
                </form>
                 {{-- @endcan --}}

            </div>
        </div>

        {{-- Details Card --}}
        <div class="theme-surface theme-title border border-gray-200/70 rounded-xl shadow-sm px-6 py-2 mt-5">
            <dl class="divide-y divide-gray-100 dark:divide-gray-800">
                
                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Name</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $district->name }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Country Id</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $district->country_id }}</dd>
                </div>

            </dl>
        </div>
    </div>
@endsection
