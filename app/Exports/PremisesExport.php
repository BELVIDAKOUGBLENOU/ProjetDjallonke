<?php

namespace App\Exports;

use App\Models\Premise;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PremisesExport implements FromQuery, WithHeadings, WithMapping
{
    protected $communityId;

    public function __construct($communityId)
    {
        $this->communityId = $communityId;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return Premise::query()
            ->where('community_id', $this->communityId)
            ->withCount('animals');
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
            'Animals Count',
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
            $premise->animals_count,
            $premise->created_at,
        ];
    }
}
