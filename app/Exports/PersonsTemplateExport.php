<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class PersonsTemplateExport implements WithHeadings
{
    public function headings(): array
    {
        return [
            'name',
            'address',
            'phone',
            'nationalId',
        ];
    }
}
