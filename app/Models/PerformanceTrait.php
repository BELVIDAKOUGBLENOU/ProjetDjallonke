<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceTrait extends Model
{
    /** @use HasFactory<\Database\Factories\PerformanceTraitFactory> */
    use HasFactory;

    const TRAIT_TYPES = ['MILK', 'WEIGHT', 'FERTILITY', 'SURVIVAL'];

    protected $fillable = [
        'performance_record_id',
        'trait_type',
        'value',
        'unit',
        'method',
    ];

    public function performanceRecord()
    {
        return $this->belongsTo(PerformanceRecord::class);
    }
    public static function getTableName()
    {
        return (new self)->getTable();
    }
}
