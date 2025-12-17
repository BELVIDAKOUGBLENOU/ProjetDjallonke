@extends('layouts.app')
@section('pageTitle', 'Prévisualisation de l\'importation')

@section('content')
    <div x-data="{ activeTab: 'valid' }">
        <div class="mb-6">
            <h1 class="text-lg font-semibold tracking-tight theme-title !mt-0">Prévisualisation de l'importation</h1>
            <p class="mb-2 text-sm theme-muted-text">Vérifiez les données avant de confirmer l'importation.</p>
        </div>

        <div class="mb-4 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" role="tablist">
                <li class="mr-2" role="presentation">
                    <button @click="activeTab = 'valid'"
                        :class="{ 'border-indigo-600 text-indigo-600': activeTab === 'valid', 'border-transparent text-gray-500 hover:text-gray-600 hover:border-gray-300': activeTab !== 'valid' }"
                        class="inline-block p-4 border-b-2 rounded-t-lg" type="button" role="tab">
                        Données valides ({{ count($validData) }})
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button @click="activeTab = 'invalid'"
                        :class="{ 'border-red-600 text-red-600': activeTab === 'invalid', 'border-transparent text-gray-500 hover:text-gray-600 hover:border-gray-300': activeTab !== 'invalid' }"
                        class="inline-block p-4 border-b-2 rounded-t-lg" type="button" role="tab">
                        Données invalides ({{ count($invalidData) }})
                    </button>
                </li>
            </ul>
        </div>

        <div x-show="activeTab === 'valid'" class="p-4 rounded-lg bg-gray-50">
            @if (count($validData) > 0)
                <div class="overflow-x-auto mb-4">
                    <table class="min-w-full text-sm bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Name</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Address</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">Phone</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500">National ID</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($validData as $data)
                                <tr>
                                    <td class="px-4 py-2">{{ $data['name'] }}</td>
                                    <td class="px-4 py-2">{{ $data['address'] }}</td>
                                    <td class="px-4 py-2">{{ $data['phone'] }}</td>
                                    <td class="px-4 py-2">{{ $data['nationalId'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <form action="{{ route('people.import.confirm') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Valider l'importation
                    </button>
                </form>
            @else
                <p class="text-gray-500">Aucune donnée valide trouvée.</p>
            @endif
        </div>

        <div x-show="activeTab === 'invalid'" class="p-4 rounded-lg bg-gray-50" style="display: none;">
            @if (count($invalidData) > 0)
                <div class="mb-4">
                    <a href="{{ route('people.import.errors') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span class="ti ti-download mr-2"></span>
                        Télécharger les erreurs
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-red-50 border-b border-red-200">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-red-700">Ligne</th>
                                <th class="px-4 py-2 text-left font-medium text-red-700">Données</th>
                                <th class="px-4 py-2 text-left font-medium text-red-700">Erreurs</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($invalidData as $row)
                                <tr>
                                    <td class="px-4 py-2 font-medium">{{ $row['row'] }}</td>
                                    <td class="px-4 py-2">
                                        <ul class="list-disc list-inside text-xs text-gray-600">
                                            <li>Name: {{ $row['data']['name'] ?? '' }}</li>
                                            <li>Address: {{ $row['data']['address'] ?? '' }}</li>
                                            <li>Phone: {{ $row['data']['phone'] ?? '' }}</li>
                                            <li>National ID: {{ $row['data']['nationalId'] ?? '' }}</li>
                                        </ul>
                                    </td>
                                    <td class="px-4 py-2 text-red-600">
                                        <ul class="list-disc list-inside">
                                            @foreach ($row['errors'] as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">Aucune donnée invalide trouvée.</p>
            @endif
        </div>
    </div>
@endsection
