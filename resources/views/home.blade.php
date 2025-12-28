@extends('layouts.app')

@section('pageTitle', 'Tableau de bord')

@section('content')
    <div class="flex flex-col gap-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold">Bonjour, {{ auth()->user()->name }} <span class="inline-block">👋</span></h2>
                <p class="text-gray-500 mt-1">Voici un aperçu de vos activités et demandes.</p>
            </div>
            <div class="text-sm text-gray-500">
                {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('LL') }}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="col-span-1 md:col-span-1 bg-white rounded-xl shadow p-4 flex flex-col">
                <div class="text-xs font-semibold text-gray-500">Communautés</div>
                <div class="text-2xl font-bold mt-2">{{ $communitiesCount ?? 0 }}</div>
            </div>
            <div class="col-span-1 md:col-span-1 bg-white rounded-xl shadow p-4 flex flex-col">
                <div class="text-xs font-semibold text-gray-500">Utilisateurs</div>
                <div class="text-2xl font-bold mt-2">{{ $usersCount ?? 0 }}</div>
            </div>
            <div class="col-span-1 md:col-span-1 bg-white rounded-xl shadow p-4 flex flex-col">
                <div class="text-xs font-semibold text-gray-500">Événements en attente</div>
                <div class="text-2xl font-bold mt-2">{{ $pendingEventsCount ?? 0 }}</div>
            </div>
            <div class="col-span-1 md:col-span-1 bg-white rounded-xl shadow p-4 flex flex-col">
                <div class="text-xs font-semibold text-gray-500">Locaux / Premises</div>
                <div class="text-2xl font-bold mt-2">{{ $premisesCount ?? 0 }}</div>
            </div>
            <div class="col-span-1 md:col-span-1 bg-white rounded-xl shadow p-4 flex flex-col">
                <div class="text-xs font-semibold text-gray-500">Animaux</div>
                <div class="text-2xl font-bold mt-2">{{ $animalsCount ?? 0 }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
            <div class="md:col-span-2 bg-white rounded-xl shadow p-6">
                <div class="font-semibold mb-4">Évolution des enregistrements (30 derniers jours)</div>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="text-sm text-gray-600">Animaux enregistrés</label>
                        <canvas id="animalsChart" height="120"></canvas>
                    </div>
                    <div>
                        <label class="text-sm text-gray-600">Événements enregistrés</label>
                        <canvas id="eventsChart" height="120"></canvas>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-6 flex flex-col gap-4">
                <div class="font-semibold">Actions rapides</div>
                <a href="{{ route('communities.create') }}"
                    class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 transition">
                    <i class="ti ti-users-plus text-xl text-green-500"></i>
                    <div>
                        <div class="font-medium">Créer une communauté</div>
                        <div class="text-xs text-gray-500">Ajouter une nouvelle communauté</div>
                    </div>
                </a>
                <a href="{{ route('users.create') }}"
                    class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 transition">
                    <i class="ti ti-user-plus text-xl text-blue-500"></i>
                    <div>
                        <div class="font-medium">Ajouter un utilisateur</div>
                        <div class="text-xs text-gray-500">Créer un compte et lui attribuer des rôles</div>
                    </div>
                </a>
                <a href="{{ route('animals.create') }}"
                    class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 transition">
                    <i class="ti ti-bone text-xl text-indigo-500"></i>
                    <div>
                        <div class="font-medium">Enregistrer un animal</div>
                        <div class="text-xs text-gray-500">Ajouter un nouvel animal au registre</div>
                    </div>
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 transition">
                    <i class="ti ti-calendar-event text-xl text-purple-500"></i>
                    <div>
                        <div class="font-medium">Créer un événement</div>
                        <div class="text-xs text-gray-500">Enregistrer un événement pour un animal</div>
                    </div>
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 transition">
                    <i class="ti ti-upload text-xl text-gray-600"></i>
                    <div>
                        <div class="font-medium">Importer des données</div>
                        <div class="text-xs text-gray-500">Importer animaux, personnes ou lieux</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        (function() {
            const labels = @json($dates ?? []);
            const animalsData = @json($animalsData ?? []);
            const eventsData = @json($eventsData ?? []);

            const createLine = (elId, label, data, color) => {
                const el = document.getElementById(elId);
                if (!el) return;
                new Chart(el, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: label,
                            data: data,
                            borderColor: color,
                            backgroundColor: color.replace('1)', '0.08)').replace('rgb', 'rgba'),
                            tension: 0.2,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                display: true
                            },
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            };

            createLine('animalsChart', 'Nouveaux animaux', animalsData, 'rgb(59,130,246)');
            createLine('eventsChart', 'Événements enregistrés', eventsData, 'rgb(139,92,246)');
        })();
    </script>
@endpush
