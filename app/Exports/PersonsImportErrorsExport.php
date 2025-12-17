<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PersonsImportErrorsExport implements FromCollection, WithHeadings, WithMapping
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
            'Name',
            'Address',
            'Phone',
            'National ID',
            'Errors',
        ];
    }

    public function map($row): array
    {
        return [
            $row['row'],
            $row['data']['name'] ?? '',
            $row['data']['address'] ?? '',
            $row['data']['phone'] ?? '',
            $row['data']['nationalId'] ?? '',
            implode(', ', $row['errors']),
        ];
    }
}
