<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthEvent extends Model
{
    /** @use HasFactory<\Database\Factories\HealthEventFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['event_id', 'health_type', 'product', 'result'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    public static function getTableName()
    {
        return (new self)->getTable();
    }
}
