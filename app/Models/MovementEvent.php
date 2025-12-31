<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovementEvent extends Model
{
    /** @use HasFactory<\Database\Factories\MovementEventFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'from_premises_id',
        'to_premises_id',
        'change_owner',
        'change_keeper',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function fromPremise()
    {
        return $this->belongsTo(Premise::class, 'from_premises_id');
    }

    public function toPremise()
    {
        return $this->belongsTo(Premise::class, 'to_premises_id');
    }
    public static function getTableName()
    {
        return (new self)->getTable();
    }
}
