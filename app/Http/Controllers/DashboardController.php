<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetCommunityContextFrontend;
use App\Models\Animal;
use App\Models\BirthEvent;
use App\Models\Community;
use App\Models\CommunityMembership;
use App\Models\DeathEvent;
use App\Models\Event;
use App\Models\HealthEvent;
use App\Models\Premise;
use App\Models\TransactionEvent;
use App\Models\User;
use App\Models\WeightRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(SetCommunityContextFrontend::class);

    }
    /**
     * Dashboard pour le Super-admin (Vue Globale)
     */
    public function superAdminStats()
    {
        if (getPermissionsTeamId() != 0) {
            return response()->json(['message' => 'Unauthorized. This dashboard is only accessible to super-admins.'], 403);
        }
        // 1. Indicateurs Clés (KPIs)
        $totalCommunities = Community::count();
        $totalAnimals = Animal::count();
        $totalUsers = User::count();
        $totalPremises = Premise::count();

        // 2. Graphiques & Visualisations

        // Courbe de Croissance (Derniers 6 mois)
        $months = collect(range(0, 5))->map(function ($i) {
            return Carbon::now()->subMonths($i)->format('Y-m');
        })->reverse()->values();

        $animalGrowth = Animal::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->pluck('count', 'month');

        $userGrowth = User::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->pluck('count', 'month');

        $growthTrends = [
            'labels' => $months,
            'animals_data' => $months->map(fn($m) => $animalGrowth[$m] ?? 0),
            'users_data' => $months->map(fn($m) => $userGrowth[$m] ?? 0),
        ];

        // Répartition des Espèces
        $speciesDistribution = Animal::select('species', DB::raw('count(*) as count'))
            ->groupBy('species')
            ->pluck('count', 'species'); // Ex: ['OVINE' => 150, 'CAPRINE' => 50]

        // 3. Activités Récentes
        $recentCommunities = Community::latest()->take(5)->get(['id', 'name', 'created_at']);

        return response()->json([
            'kpis' => [
                'total_communities' => $totalCommunities,
                'total_animals' => $totalAnimals,
                'total_users' => $totalUsers,
                'total_premises' => $totalPremises,
            ],
            'charts' => [
                'growth_trends' => $growthTrends,
                'species_distribution' => $speciesDistribution,
            ],
            'recent_activity' => [
                'new_communities' => $recentCommunities,
            ],
        ]);
    }

    /**
     * Dashboard pour les autres rôles (Community Admin, Vet, Tech, Researcher)
     */
    public function communityStats(Request $request)
    {
        $user = $request->user();

        // Déterminer le rôle principal d'après Spatie Permission
        $roles = $user->getRoleNames();

        // Note: Adapter les noms de rôles selon votre base de données (ex: 'Community Admin' vs 'COMMUNITY_ADMIN')
        if ($roles->contains('COMMUNITY_ADMIN') || $roles->contains('COMMUNITY_ADMIN')) {
            $com = Community::findOrFail(getPermissionsTeamId());
            return $this->getCommunityAdminStats($com);
        } elseif ($roles->intersect(['VET', 'TECHNICIAN', 'RESEARCHER', 'Veterinaire', 'Technician', 'Researcher'])->isNotEmpty()) {
            return $this->getTechnicalStats($user);
        } elseif ($roles->contains('FARMER')) {
            return response()->json(['message' => 'Farmer dashboard coming soon.']);
        }

        return response()->json(['message' => 'Role not supported for dashboard or no specific dashboard available.'], 403);
    }

    private function getCommunityAdminStats(Community $community)
    {

        if (!$community) {
            return response()->json(['message' => 'User is not associated with any community.'], 404);
        }
        $communityId = $community->id;

        // 1. KPIs
        $communityMembers = CommunityMembership::where('community_id', $communityId)->count();

        // Cheptel: Animaux liés aux prémises de la communauté
        $communityPremisesIds = Premise::where('community_id', $communityId)->pluck('id');
        $communityLivestock = Animal::whereIn('premises_id', $communityPremisesIds)->count();

        $activePremises = Premise::where('community_id', $communityId)->count();

        // Taux de Mortalité (Année en cours)
        $yearStart = Carbon::now()->startOfYear();

        $deaths = DeathEvent::join('events', 'events.id', '=', 'death_events.event_id')
            ->join('animals', 'animals.id', '=', 'events.animal_id')
            ->whereIn('animals.premises_id', $communityPremisesIds)
            ->where('events.event_date', '>=', $yearStart)
            ->count();

        $mortalityRate = $communityLivestock > 0 ? round(($deaths / $communityLivestock) * 100, 2) : 0;

        // 2. Graphiques Opérationnels

        // Structure de la Population
        $oneYearAgo = Carbon::now()->subYear();

        $stats = Animal::whereIn('premises_id', $communityPremisesIds)
            ->selectRaw("
                SUM(CASE WHEN sex = 'M' AND birth_date > ? THEN 1 ELSE 0 END) as young_males,
                SUM(CASE WHEN sex = 'M' AND birth_date <= ? THEN 1 ELSE 0 END) as adult_males,
                SUM(CASE WHEN sex = 'F' AND birth_date > ? THEN 1 ELSE 0 END) as young_females,
                SUM(CASE WHEN sex = 'F' AND birth_date <= ? THEN 1 ELSE 0 END) as adult_females
            ", [$oneYearAgo, $oneYearAgo, $oneYearAgo, $oneYearAgo])
            ->first();

        $populationStructure = [
            'males' => ['young' => (int) ($stats->young_males ?? 0), 'adults' => (int) ($stats->adult_males ?? 0)],
            'females' => ['young' => (int) ($stats->young_females ?? 0), 'adults' => (int) ($stats->adult_females ?? 0)],
        ];

        // Bilan des Événements
        $births = BirthEvent::join('events', 'events.id', '=', 'birth_events.event_id')
            ->join('animals', 'animals.id', '=', 'events.animal_id')
            ->whereIn('animals.premises_id', $communityPremisesIds)
            ->where('events.event_date', '>=', $yearStart)
            ->count();

        $sales = 0;
        if (class_exists(TransactionEvent::class)) {
            $sales = TransactionEvent::join('events', 'events.id', '=', 'transaction_events.event_id')
                ->join('animals', 'animals.id', '=', 'events.animal_id')
                ->whereIn('animals.premises_id', $communityPremisesIds)
                ->where('events.event_date', '>=', $yearStart)
                ->where('transaction_events.transaction_type', 'SALE')
                ->count();
        }

        $eventsSummary = [
            'births' => $births,
            'deaths' => $deaths,
            'sales' => $sales
        ];

        // 3. Listes & Actions
        $recentRegistrations = CommunityMembership::with('user:id,name,email,created_at')
            ->where('community_id', $communityId)
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'role' => 'Community Admin',
            'community_id' => $communityId,
            'kpis' => [
                'members_count' => $communityMembers,
                'livestock_count' => $communityLivestock,
                'active_premises' => $activePremises,
                'mortality_rate' => $mortalityRate . '%'
            ],
            'charts' => [
                'population_structure' => $populationStructure,
                'events_summary' => $eventsSummary
            ],
            'recent_registrations' => $recentRegistrations
        ]);
    }

    private function getTechnicalStats(User $user)
    {
        // Récupérer les communautés liées à l'utilisateur (Tech/Vet)
        $communityIds = $user->communities()->pluck('communities.id');
        $premiseIds = Premise::whereIn('community_id', $communityIds)->pluck('id');

        // 1. Validations & Tâches
        // Evénements non confirmés
        $pendingEvents = Event::whereHas('animal', function ($q) use ($premiseIds) {
            $q->whereIn('premises_id', $premiseIds);
        })->where('is_confirmed', false)->count();

        // 2. Statistiques de Santé
        $recentHealthEvents = 0;
        // Vérifier le modèle HealthEvent
        if (class_exists(HealthEvent::class)) {
            $recentHealthEvents = HealthEvent::join('events', 'events.id', '=', 'health_events.event_id')
                ->join('animals', 'animals.id', '=', 'events.animal_id')
                ->whereIn('animals.premises_id', $premiseIds)
                ->where('events.event_date', '>=', Carbon::now()->subMonth())
                ->count();
        }

        // 3. Métriques de Performance
        // Gain Moyen Quotidien (Exemple simplifié)
        $avgWeight = WeightRecord::join('events', 'events.id', '=', 'weight_records.event_id')
            ->join('animals', 'animals.id', '=', 'events.animal_id')
            ->whereIn('animals.premises_id', $premiseIds)
            ->avg('weight_records.weight');

        // Convert to float if null
        $avgWeight = $avgWeight ?? 0;

        return response()->json([
            'role' => 'Technical/Scientific',
            'tasks' => [
                'pending_validations_count' => $pendingEvents,
                'data_alerts_count' => 0, // À implémenter
            ],
            'health_stats' => [
                'recent_health_events_count' => $recentHealthEvents,
            ],
            'performance_metrics' => [
                'avg_weight' => round($avgWeight, 2),
            ]
        ]);
    }
}
