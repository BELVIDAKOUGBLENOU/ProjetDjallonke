<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class AnimalsTemplateExport implements WithHeadings
{
    public function headings(): array
    {
        return [
            'premises_code',
            'species',
            'sex',
            'birth_date',
            'life_status',
        ];
    }
}
