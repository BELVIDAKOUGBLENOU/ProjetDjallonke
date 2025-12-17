<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PremisesImportErrorsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $errors;

    public function __construct($errors)
    {
        $this->errors = collect($errors);
    }

    public function collection()
    {
        return $this->errors;
    }

    public function headings(): array
    {
        return [
            'Row',
            'Village ID',
            'Code',
            'Address',
            'GPS Coordinates',
            'Type',
            'Health Status',
            'Errors',
        ];
    }

    public function map($row): array
    {
        return [
            $row['row'],
            $row['data']['village_id'] ?? '',
            $row['data']['code'] ?? '',
            $row['data']['address'] ?? '',
            $row['data']['gps_coordinates'] ?? '',
            $row['data']['type'] ?? '',
            $row['data']['health_status'] ?? '',
            implode(', ', $row['errors']),
        ];
    }
}
