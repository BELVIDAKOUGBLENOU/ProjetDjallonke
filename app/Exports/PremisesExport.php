<?php

namespace App\Exports;

use App\Models\Premise;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PremisesExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Premise::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Village ID',
            'Code',
            'Address',
            'GPS Coordinates',
            'Type',
            'Health Status',
            'Created At',
        ];
    }

    public function map($premise): array
    {
        return [
            $premise->id,
            $premise->village_id,
            $premise->code,
            $premise->address,
            $premise->gps_coordinates,
            $premise->type,
            $premise->health_status,
            $premise->created_at,
        ];
    }
}
