@extends('layouts.app-d')
@section('pageTitle', 'Details - Premise ')
@section('content')
    <div class="active-nav-custom-url" url="{{ route('premises.index') }}"></div>

    <div class="">
        {{-- Header / Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight theme-title">Détails Premise</h1>
                <p class="mt-0 text-sm .theme-muted-text">Détails et informations de la Premise.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 justify-end">
                <a href="{{ route('premises.index') }}"
                    class="flex items-center gap-2 rounded-md border border-gray-300 theme-surface theme-title px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <span class="ti ti-arrow-left text-base"></span>
                    Retour
                </a>
                @can('update ' . \App\Models\Premise::getTableName())
                    <a href="{{ route('premises.edit', $premise->id) }}"
                        class="flex items-center gap-2 rounded-md border border-gray-300 theme-surface theme-title px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <span class="ti ti-edit text-base"></span>
                        Modifier
                    </a>
                @endcan

                @can('delete ' . \App\Models\Premise::getTableName())
                    <form action="{{ route('premises.destroy', $premise->id) }}" method="POST"
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
                @endcan

            </div>
        </div>

        {{-- Details Card --}}
        <div class="theme-surface theme-title border border-gray-200/70 rounded-xl shadow-sm px-6 py-2 mt-5">
            <dl class="divide-y divide-gray-100 dark:divide-gray-800">

                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Village Id</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $premise->village_id }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Created By</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $premise->created_by }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Community Id</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $premise->community_id }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Code</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $premise->code }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Address</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $premise->address }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Gps Coordinates</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $premise->gps_coordinates }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Type</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $premise->type }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Health Status</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $premise->health_status }}</dd>
                </div>

            </dl>
        </div>

        {{-- Keepers Section --}}
        <div class="mt-8" x-data="{
            showModal: false,
            isEdit: false,
            formAction: '',
            formMethod: 'POST',
            personId: '',
            startDate: '',
            endDate: '',
            modalTitle: 'Ajouter un Keeper',
            openAddModal() {
                this.showModal = true;
                this.isEdit = false;
                this.formAction = '{{ route('premises.keepers.store', $premise->id) }}';
                this.formMethod = 'POST';
                this.personId = '';
                this.startDate = '';
                this.endDate = '';
                this.modalTitle = 'Ajouter un Keeper';
            },
            openEditModal(keeper) {
                this.showModal = true;
                this.isEdit = true;
                this.formAction = '/premises-keepers/' + keeper.id;
                this.formMethod = 'PUT';
                this.personId = keeper.person_id;
                this.startDate = keeper.start_date;
                this.endDate = keeper.end_date;
                this.modalTitle = 'Modifier le Keeper';
            }
        }">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold theme-title">Premises Keepers</h2>
                <button @click="openAddModal()"
                    class="flex items-center gap-2 rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <span class="ti ti-plus"></span>
                    Ajouter
                </button>
            </div>

            <div class="theme-surface border border-gray-200/70 rounded-xl shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Personne
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date
                                de
                                début</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date
                                de
                                fin</th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                        @forelse ($premise->keepers as $keeper)
                            <tr>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $keeper->person->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $keeper->start_date }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $keeper->end_date ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="openEditModal({{ $keeper }})"
                                        class="text-green-600 hover:text-green-900 mr-3">Modifier</button>
                                    <form action="{{ route('premises-keepers.destroy', $keeper->id) }}" method="POST"
                                        class="inline-block" onsubmit="return confirm('Êtes-vous sûr ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Aucun keeper
                                    enregistré.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Modal --}}
            <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showModal" @click="showModal = false" class="fixed inset-0 transition-opacity"
                        aria-hidden="true">
                        <div class="absolute inset-0 bg-black/40 backdrop-blur-md"></div>
                    </div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showModal"
                        class="relative z-10 inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form :action="formAction" method="POST">
                            @csrf
                            <input type="hidden" name="_method" :value="formMethod === 'PUT' ? 'PUT' : 'POST'">

                            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
                                    x-text="modalTitle"></h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="person_id"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Personne</label>
                                        <select name="person_id" id="person_id" x-model="personId"
                                            style="min-width: 100% !important;"
                                            class="select2 mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            required>
                                            <option value="">Sélectionner une personne</option>
                                            @foreach ($persons as $person)
                                                <option value="{{ $person->id }}">{{ $person->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="start_date"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de
                                            début</label>
                                        <input type="date" name="start_date" id="start_date" x-model="startDate"
                                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                            required>
                                    </div>
                                    <div>
                                        <label for="end_date"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de
                                            fin</label>
                                        <input type="date" name="end_date" id="end_date" x-model="endDate"
                                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Enregistrer
                                </button>
                                <button type="button" @click="showModal = false"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                                    Annuler
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
