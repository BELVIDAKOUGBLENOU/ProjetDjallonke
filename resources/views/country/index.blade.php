@extends('layouts.app')
@section('pageTitle', 'Liste des Countries ')

@section('content')

    @php
        /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $countries */
        $isPaginator = is_object($countries) && method_exists($countries, 'firstItem');
        $firstItem = $isPaginator ? $countries->firstItem() ?? 1 : 1;
        $totalItems = $isPaginator
            ? $countries->total()
            : ($countries instanceof \Illuminate\Support\Collection
                ? $countries->count()
                : 0);
        $q = request('q');
    @endphp

    <div>
        <h1 class="text-lg font-semibold tracking-tight theme-title !mt-0">Countries</h1>
        <p class="mb-2 text-sm theme-muted-text">Gérez les différentes Countries de la plateforme.</p>
    </div>
    {{-- Search / Filters --}}
    <div class="theme-surface backdrop-blur-sm border border-gray-200 rounded-xl p-4 shadow-sm mb-4">
        <form method="GET" action="{{ route('countries.index') }}" class="flex  gap-3 ">
            <div class="w-[300px] relative">

                <label for="q" class="sr-only">Recherche</label>
                <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Rechercher ..."
                    class="w-full rounded-lg border-gray-200 bg-white/70 focus:bg-white px-4 py-2.5 shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm" />
                @if (request('q'))
                    <button type="button" onclick="document.getElementById('q').value=''; this.closest('form').submit();"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                        title="Effacer">
                        <span class="ti ti-x"></span>
                    </button>
                @endif
            </div>
            <div class=" gap-2">

                @if (request('q'))
                    <a href="{{ route('countries.index') }}"
                        class="inline-flex items-center rounded-md px-3 py-2.5 text-sm font-medium bg-slate-200 hover:bg-slate-300 text-gray-800 ">
                        Réinitialiser
                    </a>
                @endif
            </div>
            <div class="flex-1"></div>
            {{-- @can('create ' . \App\Models\Country::getTableName()) --}}

            <a href="{{ route('countries.create') }}"
                class="inline-flex items-center gap-2 rounded-md  px-4 py-1 text-sm font-medium text-black bg-secondary  focus:outline-none focus:ring-2 ">
                <span class="ti ti-plus text-base"></span>
                Nouveau
            </a>
            {{-- @endcan --}}

        </form>
    </div>
    <div class="">


        {{-- Table --}}
        <div class="theme-surface theme-title border border-gray-200/70 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class=" border-b border-gray-200 text-[11px] uppercase tracking-wide text-gray-500">
                        <tr>
                            <th class="py-3 pl-4 pr-3 text-left font-medium">#</th>

                            <th scope="col"
                                class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Name</th>
                            <th scope="col"
                                class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Code Iso</th>
                            <th scope="col"
                                class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Flag</th>
                            <th scope="col"
                                class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Status</th>

                            <th class="py-3 px-3 text-right font-medium w-px">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($countries as $country)
                            <tr class="group hover:bg-indigo-50/40 transition-colors">
                                <td class="whitespace-nowrap py-3 pl-4 pr-3 font-semibold text-gray-700">
                                    {{ $firstItem + $loop->index }}</td>

                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $country->name }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $country->code_iso }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-xl">
                                    {!! $country->emoji !!}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    @if ($country->is_active)
                                        <span
                                            class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Active</span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">Inactive</span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-3 py-3 text-right">
                                    <div
                                        class="flex items-center justify-end gap-1 opacity-70 group-hover:opacity-100 transition">
                                        {{-- @can('view ' . \App\Models\Country::getTableName()) --}}

                                        <a href="{{ route('countries.show', $country->id) }}" title="{{ __('Show') }}"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-md text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition">
                                            <span class="ti ti-eye text-[17px]"></span>
                                            <span class="sr-only">{{ __('Show') }}</span>
                                        </a>
                                        {{-- @endcan --}}
                                        {{-- @can('update ' . \App\Models\Country::getTableName()) --}}
                                        <a href="{{ route('countries.edit', $country->id) }}" title="{{ __('Edit') }}"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-md text-slate-500 hover:text-amber-600 hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-500/40 transition">
                                            <span class="ti ti-edit text-[17px]"></span>
                                            <span class="sr-only">{{ __('Edit') }}</span>
                                        </a>
                                        {{-- @endcan --}}

                                        {{-- @can('delete ' . \App\Models\Country::getTableName()) --}}
                                        <form action="{{ route('countries.destroy', $country->id) }}" method="POST"
                                            class="inline-flex"
                                            onsubmit="return confirmDeletion(event, 'Suppresion', 'Voulez-vous vraiment supprimer cette donnée, Cette action est irréversible ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="{{ __('Delete') }}"
                                                class="w-8 h-8 inline-flex items-center justify-center rounded-md text-slate-500 hover:text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500/40 transition">
                                                <span class="ti ti-trash text-[17px]"></span>
                                                <span class="sr-only">{{ __('Delete') }}</span>
                                            </button>
                                        </form>
                                        {{-- @endcan --}}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100" class="py-12 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="ti ti-database-off text-2xl text-gray-300"></span>
                                        <p class="font-medium">{{ __('No records found') }}@if ($q)
                                                {{ __('for') }} « {{ $q }} »
                                            @endif.</p>
                                        <div class="flex gap-2 mt-2">
                                            @if ($q)
                                                <a href="{{ route('countries.index') }}"
                                                    class="text-xs font-medium text-indigo-600 hover:text-indigo-800">{{ __('Clear search') }}</a>
                                            @endif
                                            <a href="{{ route('countries.create') }}"
                                                class="text-xs font-medium text-gray-600 hover:text-gray-900">{{ __('Create first') }}</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div
                class="px-4 py-4 border-t border-gray-100 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between ">
                <div class="text-xs text-gray-500 order-2 sm:order-1">
                    @if ($totalItems > 0)
                        @if ($isPaginator)
                            {{ __('Showing') }} {{ $countries->firstItem() }} - {{ $countries->lastItem() }}
                            {{ __('of') }} {{ $countries->total() }}
                        @else
                            {{ __('Total') }} : {{ $totalItems }}
                        @endif
                    @else
                        {{ __('No entries to display') }}
                    @endif
                </div>
                <div class="order-1 sm:order-2">
                    @if ($isPaginator)
                        {!! $countries->withQueryString()->links() !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
