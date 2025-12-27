<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Community;
use App\Models\User;
use App\Models\Event;
use App\Models\Premise;
use App\Models\Animal;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $today = now();

        $communitiesCount = Community::count();
        $usersCount = User::count();
        $pendingEventsCount = Event::where('is_confirmed', 0)->count();
        $premisesCount = Premise::count();
        $animalsCount = Animal::count();

        // Last 30 days labels
        $start = $today->copy()->subDays(29)->startOfDay();

        $dates = [];
        for ($i = 0; $i < 30; $i++) {
            $dates[] = $start->copy()->addDays($i)->format('Y-m-d');
        }

        // Animals created per day
        $animalsSeries = DB::table('animals')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$start->toDateTimeString(), $today->endOfDay()->toDateTimeString()])
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Events created per day
        $eventsSeries = DB::table('events')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$start->toDateTimeString(), $today->endOfDay()->toDateTimeString()])
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Map to full 30-day arrays
        $animalsData = array_map(function ($d) use ($animalsSeries) {
            return isset($animalsSeries[$d]) ? (int) $animalsSeries[$d] : 0;
        }, $dates);

        $eventsData = array_map(function ($d) use ($eventsSeries) {
            return isset($eventsSeries[$d]) ? (int) $eventsSeries[$d] : 0;
        }, $dates);

        return view('home', compact(
            'communitiesCount',
            'usersCount',
            'pendingEventsCount',
            'premisesCount',
            'animalsCount',
            'dates',
            'animalsData',
            'eventsData'
        ));
    }
}
