<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeathEvent extends Model
{
    /** @use HasFactory<\Database\Factories\DeathEventFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['event_id', 'cause', 'death_place'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    public static function getTableName()
    {
        return (new self)->getTable();
    }
}
