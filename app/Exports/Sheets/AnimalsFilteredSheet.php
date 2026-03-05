<?php

namespace App\Exports\Sheets;

use App\Models\Animal;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class AnimalsFilteredSheet implements FromQuery, WithTitle, WithHeadings, WithMapping
{
    protected $filters;
    protected $communityId;

    public function __construct($filters, $communityId)
    {
        $this->filters = $filters;
        $this->communityId = $communityId;
    }

    public function query()
    {
        $query = Animal::query()
            ->with(['premise', 'identifiers', 'personRoles.person']);

        if ($this->communityId) {
            $query->whereHas('premise', function ($q) {
                $q->where('community_id', $this->communityId);
            });
        }

        if (!empty($this->filters['q'])) {
            $query->search($this->filters['q']);
        }

        if (!empty($this->filters['premises_id'])) {
            $query->where('premises_id', $this->filters['premises_id']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Local',
            'Identifiants',
            'Affiliations',
            'Espèce',
            'Sexe',
            'Age',
            'Date Naissance',
            'Statut'
        ];
    }

    public function map($animal): array
    {
        // Identifiers: Type : Code | Type 2 : Code 2
        $identifiers = $animal->identifiers->map(function ($id) {
            return $id->type . ' : ' . $id->code;
        })->implode(' | ');

        // Affiliations: Role : UID | Role 2 : UID
        $affiliations = $animal->personRoles->map(function ($role) {
            return $role->role_type . ' : ' . ($role->person ? $role->person->uid : 'N/A');
        })->implode(' | ');

        // Age calculation
        $age = $animal->birth_date ? Carbon::parse($animal->birth_date)->age : '?';

        return [
            $animal->id,
            $animal->premise ? $animal->premise->code : 'N/A',
            $identifiers,
            $affiliations,
            $animal->species,
            $animal->sex,
            $age,
            $animal->birth_date,
            $animal->life_status,
        ];
    }

    public function title(): string
    {
        return 'Animaux';
    }
}
