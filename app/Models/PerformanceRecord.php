<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceRecord extends Model
{
    /** @use HasFactory<\Database\Factories\PerformanceRecordFactory> */
    use HasFactory;

    protected $fillable = [
        'uid',
        'created_by',
        'animal_id',
        'recorded_date',
        'context',
    ];

    public static function getTableName()
    {
        return (new self)->getTable();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function traits()
    {
        return $this->hasMany(PerformanceTrait::class);
    }
}
