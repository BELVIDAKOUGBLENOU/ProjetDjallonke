<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Models\Animal;
use App\Models\AnimalIdentifier;
use App\Models\Community;
use App\Models\CommunityMembership;
use App\Models\Country;
use App\Models\Event;
use App\Models\HealthEvent;
use App\Models\PerformanceTrait;
use App\Models\PersonRole;
use App\Models\Premise;
use App\Models\ReproductionEvent;
use App\Models\TransactionEvent;
use PHPUnit\Framework\Constraint\Count;

class ConstantDataController extends Controller
{
    public function getCountries()
    {
        $countries = Country::where('is_active', true)
            ->with('districts.subDistricts.villages')
            ->orderBy('name')->get();
        $data = CountryResource::collection($countries);
        $data->each(fn($r) => $r->setImbriqued(true));
        return $data;
    }

    function getConstants()
    {
        $data = [];
        $data['community_roles'] = CommunityMembership::ROLES;
        $data['premise_types'] = Premise::TYPES;
        $data['animal_species'] = Animal::SPECIES;
        $data['animal_sexes'] = Animal::SEXES;
        $data['animal_life_statuses'] = Animal::LIFE_STATUSES;
        $data['identifier_types'] = AnimalIdentifier::TYPES;
        $data['person_role_types'] = PersonRole::ROLE_TYPES;
        $data['event_sources'] = Event::SOURCES;
        $data['health_event_types'] = HealthEvent::HEALTH_TYPES;
        $data['transaction_types'] = TransactionEvent::TRANSACTION_TYPES;
        $data['reproduction_types'] = ReproductionEvent::REPRO_TYPES;
        $data['performance_trait_types'] = PerformanceTrait::TRAIT_TYPES;

        return response()->json($data);
    }
}
