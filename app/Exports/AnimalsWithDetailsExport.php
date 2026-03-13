<?php

namespace App\Exports;

use App\Exports\Sheets\AnimalsFilteredSheet;
use App\Exports\Sheets\PersonsReferenceSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AnimalsWithDetailsExport implements WithMultipleSheets
{
    protected $filters;

    protected $communityId;

    public function __construct(array $filters, $communityId)
    {
        $this->filters = $filters;
        $this->communityId = $communityId;
    }

    public function sheets(): array
    {
        return [
            new AnimalsFilteredSheet($this->filters, $this->communityId),
            new PersonsReferenceSheet($this->communityId),
        ];
    }
}
