<?php

namespace App\Exports;

use App\Models\Animal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AnimalsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Animal::with(['creator', 'premise'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'UID',
            'Created By',
            'Premise Code',
            'Species',
            'Sex',
            'Birth Date',
            'Life Status',
            'Created At',
        ];
    }

    public function map($animal): array
    {
        return [
            $animal->id,
            $animal->uid,
            $animal->creator?->name,
            $animal->premise?->code,
            $animal->species,
            $animal->sex,
            $animal->birth_date,
            $animal->life_status,
            $animal->created_at,
        ];
    }
}
