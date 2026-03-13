<?php

namespace App\Exports;

use App\Models\Person;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PersonsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $communityId;

    public function __construct($communityId)
    {
        $this->communityId = $communityId;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $communityId = $this->communityId;
        $query = Person::query()
            ->withCount([
                'personRoles as owned_animals_count' => function ($query) {
                    $query->where('role_type', 'OWNER');
                },
            ])
            ->withCount('personRoles as related_animals_count');

        if ($communityId && $communityId != 0) {
            $query->whereHas('personRoles', function ($q) use ($communityId) {
                $q->whereHas('animal', function ($q) use ($communityId) {
                    $q->whereHas('premise', function ($q) use ($communityId) {
                        $q->where('community_id', $communityId);
                    });
                });
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Address',
            'Phone',
            'National ID',
            'Owned Animals Count',
            'Related Animals Count',
            'Created At',
        ];
    }

    public function map($person): array
    {
        return [
            $person->id,
            $person->name,
            $person->address,
            $person->phone,
            $person->nationalId,
            $person->owned_animals_count,
            $person->related_animals_count,
            $person->created_at,
        ];
    }
}
