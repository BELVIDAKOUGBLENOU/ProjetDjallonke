@extends('layouts.app-d')
@section('pageTitle', 'Details - Animal ')
@section('content')
    <div class="active-nav-custom-url" url="{{ route('animals.index') }}"></div>

    <div class="">
        {{-- Header / Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight theme-title">Détails Animal</h1>
                <p class="mt-0 text-sm .theme-muted-text">Détails et informations de la Animal.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 justify-end">
                <a href="{{ route('animals.index') }}"
                    class="flex items-center gap-2 rounded-md border border-gray-300 theme-surface theme-title px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <span class="ti ti-arrow-left text-base"></span>
                    Retour
                </a>
                {{-- @can('update ' . \App\Models\Animal::getTableName()) --}}

                <a href="{{ route('animals.edit', $animal->id) }}"
                    class="flex items-center gap-2 rounded-md border border-gray-300 theme-surface theme-title px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <span class="ti ti-edit text-base"></span>
                    Modifier
                </a>
                {{-- @endcan --}}

                {{-- @can('delete ' . \App\Models\Animal::getTableName()) --}}

                <form action="{{ route('animals.destroy', $animal->id) }}" method="POST"
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
                {{-- @endcan --}}

            </div>
        </div>

        {{-- Details Card --}}
        <div class="theme-surface theme-title border border-gray-200/70 rounded-xl shadow-sm px-6 py-2 mt-5">
            <dl class="divide-y divide-gray-100 dark:divide-gray-800">

                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Uid</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $animal->uid }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Created By</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $animal->created_by }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Premises Id</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $animal->premises_id }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Species</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $animal->species }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Sex</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $animal->sex }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Birth Date</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $animal->birth_date }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Life Status</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $animal->life_status }}</dd>
                </div>

            </dl>
        </div>

        {{-- Identifiers Section --}}
        <div class="mt-8" x-data="{
            showAddModal: false,
            showEditModal: false,
            editIdentifier: { id: '', type: '', code: '', active: false },
            openEditModal(identifier) {
                this.editIdentifier = identifier;
                this.showEditModal = true;
            }
        }">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold theme-title">Identifiers</h2>
                <button @click="showAddModal = true"
                    class="flex items-center gap-2 rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <span class="ti ti-plus"></span>
                    Ajouter
                </button>
            </div>

            <div class="overflow-hidden rounded-lg border border-gray-200 shadow-sm theme-surface">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 theme-bg-subtle">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider theme-muted-text">
                                Type</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider theme-muted-text">
                                Code</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider theme-muted-text">
                                Active</th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider theme-muted-text">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 theme-surface theme-divide">
                        @forelse ($animal->identifiers as $identifier)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 theme-text">
                                    {{ $identifier->type }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 theme-text">
                                    {{ $identifier->code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 theme-text">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $identifier->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $identifier->active ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="openEditModal({{ $identifier }})"
                                        class="text-green-600 hover:text-green-900 mr-3">Edit</button>
                                    <form action="{{ route('animal-identifiers.destroy', $identifier->id) }}"
                                        method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center theme-muted-text">
                                    No identifiers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Add Modal --}}
            <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showAddModal" @click="showAddModal = false" class="fixed inset-0 transition-opacity"
                        aria-hidden="true">
                        <div class="absolute inset-0 bg-black/40 backdrop-blur-md"></div>
                    </div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showAddModal"
                        class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full theme-surface">
                        <form action="{{ route('animals.identifiers.store', $animal->id) }}" method="POST">
                            @csrf
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 theme-surface">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 theme-title mb-4">Add Identifier</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label for="type"
                                            class="block text-sm font-medium text-gray-700 theme-text">Type</label>
                                        <input type="text" name="type" id="type" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm theme-input">
                                    </div>
                                    <div>
                                        <label for="code"
                                            class="block text-sm font-medium text-gray-700 theme-text">Code</label>
                                        <input type="text" name="code" id="code" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm theme-input">
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="active" id="active" value="1"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                        <label for="active"
                                            class="ml-2 block text-sm text-gray-900 theme-text">Active</label>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse theme-bg-subtle">
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Save
                                </button>
                                <button type="button" @click="showAddModal = false"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm theme-surface theme-text">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Edit Modal --}}
            <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showEditModal" @click="showEditModal = false" class="fixed inset-0 transition-opacity"
                        aria-hidden="true">
                        <div class="absolute inset-0 bg-black/40 backdrop-blur-md"></div>
                    </div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showEditModal"
                        class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full theme-surface">
                        <form :action="'/animal-identifiers/' + editIdentifier.id" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 theme-surface">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 theme-title mb-4">Edit Identifier
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <label for="edit_type"
                                            class="block text-sm font-medium text-gray-700 theme-text">Type</label>
                                        <input type="text" name="type" id="edit_type"
                                            x-model="editIdentifier.type" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm theme-input">
                                    </div>
                                    <div>
                                        <label for="edit_code"
                                            class="block text-sm font-medium text-gray-700 theme-text">Code</label>
                                        <input type="text" name="code" id="edit_code"
                                            x-model="editIdentifier.code" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm theme-input">
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="active" id="edit_active" value="1"
                                            x-model="editIdentifier.active" :checked="editIdentifier.active"
                                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                        <label for="edit_active"
                                            class="ml-2 block text-sm text-gray-900 theme-text">Active</label>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse theme-bg-subtle">
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Update
                                </button>
                                <button type="button" @click="showEditModal = false"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm theme-surface theme-text">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
