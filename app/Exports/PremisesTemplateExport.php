<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;

class PremisesTemplateExport implements WithHeadings
{
    public function headings(): array
    {
        return [
            'village_id',
            'code',
            'address',
            'gps_coordinates',
            'type',
            'health_status',
        ];
    }
}
