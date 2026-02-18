<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReproductionEvent extends Model
{
    /** @use HasFactory<\Database\Factories\ReproductionEventFactory> */
    use HasFactory;

    const REPRO_TYPES = ['MATING', 'AI', 'DIAGNOSIS'];

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'mother_id',
        'father_id',
        'repro_type',
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
    public static function getTableName()
    {
        return (new self)->getTable();
    }
}
