<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class EventsExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    protected $filters;
    protected $communityId;
    protected $type;

    public function __construct(array $filters, $communityId)
    {
        $this->filters = $filters;
        $this->communityId = $communityId;
        $this->type = $filters['type'] ?? null;
    }

    public function query()
    {
        $query = Event::query()->with(['animal', 'creator', 'confirmer']);

        if ($this->communityId) {
            $query->whereHas('animal.premise', function ($q) {
                $q->where('community_id', $this->communityId);
            });
        }

        if (!empty($this->filters['q'])) {
            $q = $this->filters['q'];
            $query->where(function ($sub) use ($q) {
                $sub->where('comment', 'like', "%{$q}%")
                    ->orWhereHas('animal', function ($sq) use ($q) {
                        $sq->where('uid', 'like', "%{$q}%");
                    });
            });
        }

        if (!empty($this->filters['status'])) {
            if ($this->filters['status'] === 'pending') {
                $query->where('is_confirmed', false);
            } elseif ($this->filters['status'] === 'confirmed') {
                $query->where('is_confirmed', true);
            }
        }

        if ($this->type) {
            switch ($this->type) {
                case 'health_event':
                    $query->has('healthEvent')->with('healthEvent');
                    break;
                case 'movement_event':
                    $query->has('movementEvent')->with(['movementEvent.fromPremises', 'movementEvent.toPremises']);
                    break;
                case 'transaction_event':
                    $query->has('transactionEvent')->with(['transactionEvent.buyer', 'transactionEvent.seller']);
                    break;
                case 'reproduction_event':
                    $query->has('reproductionEvent')->with(['reproductionEvent.mother', 'reproductionEvent.father']);
                    break;
                case 'birth_event':
                    $query->has('birthEvent')->with(['birthEvent.mother', 'birthEvent.father']);
                    break;
                case 'milk_record':
                    $query->has('milkRecord')->with('milkRecord');
                    break;
                case 'weight_record':
                    $query->has('weightRecord')->with('weightRecord');
                    break;
                case 'death_event':
                    $query->has('deathEvent')->with('deathEvent');
                    break;
            }
        }

        return $query;
    }

    public function headings(): array
    {
        $baseHeadings = [
            'ID',
            'Date',
            'Animal UID',
            'Source',
            'Statut',
            'Crée par',
            'Commentaire'
        ];

        $specificHeadings = [];

        switch ($this->type) {
            case 'health_event':
                $specificHeadings = ['Type', 'Produit', 'Résultat'];
                break;
            case 'movement_event':
                $specificHeadings = ['De (Local)', 'Vers (Local)', 'Changement Propriétaire', 'Changement Détenteur'];
                break;
            case 'transaction_event':
                $specificHeadings = ['Type Transaction', 'Prix', 'Acheteur', 'Vendeur'];
                break;
            case 'reproduction_event':
                $specificHeadings = ['Type Repro', 'Mère', 'Père'];
                break;
            case 'birth_event':
                $specificHeadings = ['Nés Vivants', 'Nés Morts', 'Mère', 'Père'];
                break;
            case 'milk_record':
                $specificHeadings = ['Volume (L)', 'Période'];
                break;
            case 'weight_record':
                $specificHeadings = ['Poids', 'Age (Jours)', 'Méthode'];
                break;
            case 'death_event':
                $specificHeadings = ['Cause', 'Lieu'];
                break;
        }

        return array_merge($baseHeadings, $specificHeadings);
    }

    public function map($event): array
    {
        $baseData = [
            $event->id,
            $event->event_date,
            $event->animal?->uid ?? 'N/A',
            $event->source,
            $event->is_confirmed ? 'Confirmé' : 'En attente',
            $event->creator?->name ?? 'N/A',
            $event->comment
        ];

        $specificData = [];

        switch ($this->type) {
            case 'health_event':
                $h = $event->healthEvent;
                $specificData = [
                    $h?->health_type,
                    $h?->product,
                    $h?->result
                ];
                break;
            case 'movement_event':
                $m = $event->movementEvent;
                $specificData = [
                    $m?->fromPremises?->code,
                    $m?->toPremises?->code,
                    $m?->change_owner ? 'Oui' : 'Non',
                    $m?->change_keeper ? 'Oui' : 'Non',
                ];
                break;
            case 'transaction_event':
                $t = $event->transactionEvent;
                $specificData = [
                    $t?->transaction_type,
                    $t?->price,
                    $t?->buyer?->name,
                    $t?->seller?->name,
                ];
                break;
            case 'reproduction_event':
                $r = $event->reproductionEvent;
                $specificData = [
                    $r?->repro_type,
                    $r?->mother?->uid,
                    $r?->father?->uid,
                ];
                break;
            case 'birth_event':
                $b = $event->birthEvent;
                $specificData = [
                    $b?->nb_alive,
                    $b?->nb_dead,
                    $b?->mother?->uid,
                    $b?->father?->uid,
                ];
                break;
            case 'milk_record':
                $mk = $event->milkRecord;
                $specificData = [
                    $mk?->volume_liters,
                    $mk?->period,
                ];
                break;
            case 'weight_record':
                $w = $event->weightRecord;
                $specificData = [
                    $w?->weight,
                    $w?->age_days,
                    $w?->measure_method,
                ];
                break;
            case 'death_event':
                $d = $event->deathEvent;
                $specificData = [
                    $d?->cause,
                    $d?->death_place,
                ];
                break;
        }

        return array_merge($baseData, $specificData);
    }

    public function title(): string
    {
        return $this->type ? 'Evénements ' . strtoupper(str_replace('_', ' ', $this->type)) : 'Evénements';
    }
}
