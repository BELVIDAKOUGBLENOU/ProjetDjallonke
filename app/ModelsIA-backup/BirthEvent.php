<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BirthEvent extends Model
{
    /** @use HasFactory<\Database\Factories\BirthEventFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'mother_id',
        'father_id',
        'nb_alive',
        'nb_dead',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function mother()
    {
        return $this->belongsTo(Animal::class, 'mother_id');
    }

    public function father()
    {
        return $this->belongsTo(Animal::class, 'father_id');
    }
}
