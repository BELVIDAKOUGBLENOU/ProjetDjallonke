@extends('layouts.app-d')
@section('pageTitle', 'Details - Community')
@section('content')
    <div class="active-nav-custom-url" url="{{ route('communities.index') }}"></div>

    <div x-data="{
        showAddModal: false,
        showEditModal: false,
        role: '',
        editRole: '',
        editAction: ''
    }">
        {{-- Header / Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight theme-title">Détails Community</h1>
                <p class="mt-0 text-sm .theme-muted-text">Détails et informations de la Community.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 justify-end">
                <a href="{{ route('communities.index') }}"
                    class="flex items-center gap-2 rounded-md border border-gray-300 theme-surface theme-title px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <span class="ti ti-arrow-left text-base"></span>
                    Retour
                </a>
                <a href="{{ route('communities.edit', $community->id) }}"
                    class="flex items-center gap-2 rounded-md border border-gray-300 theme-surface theme-title px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <span class="ti ti-edit text-base"></span>
                    Modifier
                </a>
                <form action="{{ route('communities.destroy', $community->id) }}" method="POST"
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
            <dl class="divide-y divide-gray-100 dark:divide-gray-800">
                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Name</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $community->name }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Creation Date</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $community->creation_date }}</dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Created By</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $community->creator?->name ?? $community->created_by }}
                    </dd>
                </div>
                <div class="py-3 sm:grid sm:grid-cols-4 sm:gap-4">
                    <dt class="text-xs font-semibold tracking-wide opacity-80 uppercase col-span-1">Country</dt>
                    <dd class="mt-1 sm:mt-0 col-span-3 text-sm ">{{ $community->country?->name ?? $community->country_id }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Members Section --}}
        <div class="mt-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold theme-title">Members</h2>
                <button @click="showAddModal = true"
                    class="flex items-center gap-2 rounded-md bg-green-600 px-4 py-1.5 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <span class="ti ti-plus"></span>
                    Add Member
                </button>
            </div>

            <div class="theme-surface theme-title border border-gray-200/70 rounded-xl shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Added
                                At</th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($members as $member)
                            <tr>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $member->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $member->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $member->pivot->role }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $member->pivot->added_at }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button
                                        @click="showEditModal = true; editRole = '{{ $member->pivot->role }}'; editAction = '{{ route('communities.members.update', [$community->id, $member->id]) }}'"
                                        class="text-green-600 hover:text-green-900 mr-3">Edit</button>
                                    <form
                                        action="{{ route('communities.members.destroy', [$community->id, $member->id]) }}"
                                        method="POST" class="inline-block"
                                        onsubmit="return confirm('Are you sure you want to remove this member?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No
                                    members found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-3">
                    {{ $members->links() }}
                </div>
            </div>
        </div>

        {{-- Add Member Modal --}}
        <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showAddModal" @click="showAddModal = false" class="fixed inset-0 transition-opacity"
                    aria-hidden="true">
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-md"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showAddModal"
                    class="relative z-10 inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('communities.members.store', $community->id) }}" method="POST">
                        @csrf
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">Add New Member
                            </h3>

                            <div class="mb-4">
                                <label for="role"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                                <select x-model="role" name="role" id="role"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                                    required>
                                    <option value="">Select Role</option>
                                    <option value="COMMUNITY_ADMIN">Community Admin</option>
                                    <option value="FARMER">Farmer</option>
                                    <option value="VET">Vet</option>
                                    <option value="TECHNICIAN">Technician</option>
                                    <option value="RESEARCHER">Researcher</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                <input type="text" name="name" id="name"
                                    class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 rounded-md"
                                    required>
                            </div>

                            <div class="mb-4">
                                <label for="email"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <input type="email" name="email" id="email"
                                    class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 rounded-md"
                                    required>
                            </div>

                            <div x-show="role === 'FARMER'" class="space-y-4">
                                <div>
                                    <label for="phone"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                    <input type="text" name="phone" id="phone"
                                        class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 rounded-md">
                                </div>
                                <div>
                                    <label for="nationalId"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">National
                                        ID</label>
                                    <input type="text" name="nationalId" id="nationalId"
                                        class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:bg-gray-700 dark:border-gray-600 rounded-md">
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Add
                            </button>
                            <button type="button" @click="showAddModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Edit Member Modal --}}
        <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showEditModal" @click="showEditModal = false" class="fixed inset-0 transition-opacity"
                    aria-hidden="true">
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-md"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showEditModal"
                    class="relative z-10 inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form :action="editAction" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">Edit Member
                                Role</h3>

                            <div class="mb-4">
                                <label for="editRole"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                                <select x-model="editRole" name="role" id="editRole"
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                                    required>
                                    <option value="COMMUNITY_ADMIN">Community Admin</option>
                                    <option value="FARMER">Farmer</option>
                                    <option value="VET">Vet</option>
                                    <option value="TECHNICIAN">Technician</option>
                                    <option value="RESEARCHER">Researcher</option>
                                </select>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Update
                            </button>
                            <button type="button" @click="showEditModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
