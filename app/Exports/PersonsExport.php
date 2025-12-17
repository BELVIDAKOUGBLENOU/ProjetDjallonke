<?php

namespace App\Exports;

use App\Models\Person;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PersonsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Person::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Address',
            'Phone',
            'National ID',
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
            $person->created_at,
        ];
    }
}
