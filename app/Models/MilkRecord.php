<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MilkRecord extends Model
{
    /** @use HasFactory<\Database\Factories\MilkRecordFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['event_id', 'volume_liters', 'period'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    public static function getTableName()
    {
        return (new self)->getTable();
    }
}
