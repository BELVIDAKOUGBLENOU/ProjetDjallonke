<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AnimalsImportErrorsExport implements FromCollection, WithHeadings, WithMapping
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
            'Premises Code',
            'Species',
            'Sex',
            'Birth Date',
            'Life Status',
            'Errors',
        ];
    }

    public function map($row): array
    {
        return [
            $row['row'],
            $row['data']['premises_code'] ?? '',
            $row['data']['species'] ?? '',
            $row['data']['sex'] ?? '',
            $row['data']['birth_date'] ?? '',
            $row['data']['life_status'] ?? '',
            implode(', ', $row['errors']),
        ];
    }
}
