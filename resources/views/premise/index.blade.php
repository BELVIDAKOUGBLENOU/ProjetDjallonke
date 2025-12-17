@extends('layouts.app')
@section('pageTitle', 'Liste des Premises ')

@section('content')

    @php
        /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $premises */
        $isPaginator = is_object($premises) && method_exists($premises, 'firstItem');
        $firstItem = $isPaginator ? $premises->firstItem() ?? 1 : 1;
        $totalItems = $isPaginator
            ? $premises->total()
            : ($premises instanceof \Illuminate\Support\Collection
                ? $premises->count()
                : 0);
        $q = request('q');
    @endphp

    <div x-data="{ importModalOpen: false }">
        {{-- Import Modal --}}
        <div x-show="importModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="importModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity bg-black/40 backdrop-blur-md" aria-hidden="true"
                    @click="importModalOpen = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="importModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <span class="ti ti-upload text-green-600 text-xl"></span>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Importer des premises
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4">
                                        Le fichier doit contenir les colonnes suivantes :
                                    </p>
                                    <ul class="list-disc list-inside text-sm text-gray-600 mb-4 pl-2">
                                        <li>village_id</li>
                                        <li>code</li>
                                        <li>address</li>
                                        <li>gps_coordinates</li>
                                        <li>type</li>
                                        <li>health_status</li>
                                    </ul>

                                    <div class="mb-4">
                                        <a href="{{ route('premises.template') }}"
                                            class="text-green-600 hover:text-green-900 text-sm font-medium flex items-center gap-1">
                                            <span class="ti ti-download"></span>
                                            Télécharger le modèle vide
                                        </a>
                                    </div>

                                    <form action="{{ route('premises.import') }}" method="POST"
                                        enctype="multipart/form-data" id="importForm">
                                        @csrf
                                        <div
                                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                            <div class="space-y-1 text-center">
                                                <span class="ti ti-file-spreadsheet text-gray-400 text-4xl"></span>
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="file-upload"
                                                        class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                                                        <span>Télécharger un fichier</span>
                                                        <input id="file-upload" name="file" type="file"
                                                            class="sr-only" accept=".xlsx,.xls,.csv" required>
                                                    </label>
                                                    <p class="pl-1">ou glisser-déposer</p>
                                                </div>
                                                <p class="text-xs text-gray-500">
                                                    XLSX, XLS, CSV jusqu'à 10MB
                                                </p>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" onclick="document.getElementById('importForm').submit()"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Soumettre
                        </button>
                        <button type="button" @click="importModalOpen = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">

            <div>
                <h1 class="text-lg font-semibold tracking-tight theme-title !mt-0">Premises</h1>
                <p class="mb-2 text-sm theme-muted-text">Gérez les différentes Premises de la plateforme.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 justify-end">
                <a href="#" @click="importModalOpen = true"
                    class="flex items-center gap-2 rounded-md border border-gray-300 theme-surface theme-title px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <span class="ti ti-upload text-base"></span>
                    Importer des Premises
                </a>
                <a href="{{ route('premises.export') }}"
                    class="flex items-center gap-2 rounded-md border border-gray-300 theme-surface theme-title px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <span class="ti ti-download text-base"></span>
                    Exporter des Premises
                </a>


            </div>
        </div>
        {{-- Search / Filters --}}
        <div class="theme-surface backdrop-blur-sm border border-gray-200 rounded-xl p-4 shadow-sm mb-4">
            <form method="GET" action="{{ route('premises.index') }}" class="flex  gap-3 ">
                <div class="w-[300px] relative">

                    <label for="q" class="sr-only">Recherche</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}"
                        placeholder="Rechercher ..."
                        class="w-full rounded-lg border-gray-200 bg-white/70 focus:bg-white px-4 py-2.5 shadow-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm" />
                    @if (request('q'))
                        <button type="button"
                            onclick="document.getElementById('q').value=''; this.closest('form').submit();"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                            title="Effacer">
                            <span class="ti ti-x"></span>
                        </button>
                    @endif
                </div>
                <div class=" gap-2">

                    @if (request('q'))
                        <a href="{{ route('premises.index') }}"
                            class="inline-flex items-center rounded-md px-3 py-2.5 text-sm font-medium bg-slate-200 hover:bg-slate-300 text-gray-800 ">
                            Réinitialiser
                        </a>
                    @endif
                </div>
                <div class="flex-1"></div>
                {{-- @can('create ' . \App\Models\Premise::getTableName()) --}}

                <div class="flex gap-2">

                    <a href="{{ route('premises.create') }}"
                        class="inline-flex items-center gap-2 rounded-md  px-4 py-1 text-sm font-medium text-black bg-secondary  focus:outline-none focus:ring-2 ">
                        <span class="ti ti-plus text-base"></span>
                        Nouveau
                    </a>
                </div>
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
                                    Village</th>
                                <th scope="col"
                                    class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Created By</th>

                                <th scope="col"
                                    class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Code</th>

                                <th scope="col"
                                    class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Type</th>
                                <th scope="col"
                                    class="py-3 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Health Status</th>

                                <th class="py-3 px-3 text-right font-medium w-px">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($premises as $premise)
                                <tr class="group hover:bg-green-50/40 transition-colors">
                                    <td class="whitespace-nowrap py-3 pl-4 pr-3 font-semibold text-gray-700">
                                        {{ $firstItem + $loop->index }}</td>

                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $premise->village->name ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $premise->creator->name ?? '-' }}</td>

                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $premise->code }}
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $premise->type }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $premise->health_status }}</td>

                                    <td class="whitespace-nowrap px-3 py-3 text-right">
                                        <div
                                            class="flex items-center justify-end gap-1 opacity-70 group-hover:opacity-100 transition">
                                            {{-- @can('view ' . \App\Models\Premise::getTableName()) --}}

                                            <a href="{{ route('premises.show', $premise->id) }}"
                                                title="{{ __('Show') }}"
                                                class="w-8 h-8 inline-flex items-center justify-center rounded-md text-slate-500 hover:text-green-600 hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-green-500/50 transition">
                                                <span class="ti ti-eye text-[17px]"></span>
                                                <span class="sr-only">{{ __('Show') }}</span>
                                            </a>
                                            {{-- @endcan --}}
                                            {{-- @can('update ' . \App\Models\Premise::getTableName()) --}}
                                            <a href="{{ route('premises.edit', $premise->id) }}"
                                                title="{{ __('Edit') }}"
                                                class="w-8 h-8 inline-flex items-center justify-center rounded-md text-slate-500 hover:text-amber-600 hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-500/40 transition">
                                                <span class="ti ti-edit text-[17px]"></span>
                                                <span class="sr-only">{{ __('Edit') }}</span>
                                            </a>
                                            {{-- @endcan --}}

                                            {{-- @can('delete ' . \App\Models\Premise::getTableName()) --}}
                                            <form action="{{ route('premises.destroy', $premise->id) }}" method="POST"
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
                                                    <a href="{{ route('premises.index') }}"
                                                        class="text-xs font-medium text-green-600 hover:text-green-800">{{ __('Clear search') }}</a>
                                                @endif
                                                <a href="{{ route('premises.create') }}"
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
                                {{ __('Showing') }} {{ $premises->firstItem() }} - {{ $premises->lastItem() }}
                                {{ __('of') }} {{ $premises->total() }}
                            @else
                                {{ __('Total') }} : {{ $totalItems }}
                            @endif
                        @else
                            {{ __('No entries to display') }}
                        @endif
                    </div>
                    <div class="order-1 sm:order-2">
                        @if ($isPaginator)
                            {!! $premises->withQueryString()->links() !!}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
