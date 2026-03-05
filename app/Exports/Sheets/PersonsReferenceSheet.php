<?php

namespace App\Exports\Sheets;

use App\Models\Person;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class PersonsReferenceSheet implements FromQuery, WithTitle, WithHeadings, WithMapping
{
    protected $communityId;

    public function __construct($communityId)
    {
        $this->communityId = $communityId;
    }

    public function query()
    {
        $query = Person::query();

        if ($this->communityId) {
            // Fetch persons relevant to the community if possible
            // Assuming person has premises or similar?
            // Or maybe just list all persons if community logic is complex.
            // If person has animals in community premises?
            $query->whereHas('personRoles.animal.premise', function ($q) {
                $q->where('community_id', $this->communityId);
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'UID',
            'Nom',
            'Prénoms',
            'Téléphone',
        ];
    }

    public function map($person): array
    {
        return [
            $person->uid,
            $person->last_name,
            $person->first_name,
            $person->phone_number,
        ];
    }

    public function title(): string
    {
        return 'Personnes (Référence)';
    }
}
