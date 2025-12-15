<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeightRecord extends Model
{
    /** @use HasFactory<\Database\Factories\WeightRecordFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'uid',
        'event_id',
        'weight',
        'age_days',
        'measure_method',
    ];

    public static function getTableName()
    {
        return (new self)->getTable();
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
