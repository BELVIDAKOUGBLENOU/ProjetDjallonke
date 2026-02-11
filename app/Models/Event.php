<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [

        'created_by',
        'confirmed_by',
        'animal_id',
        'source',
        'event_date',
        'comment',
        'is_confirmed',
        'uid',
        'version'
    ];
    //casts
    protected $casts = [
        'is_confirmed' => 'boolean'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }

    public function healthEvent()
    {
        return $this->hasOne(HealthEvent::class);
    }

    public function movementEvent()
    {
        return $this->hasOne(MovementEvent::class);
    }

    public function transactionEvent()
    {
        return $this->hasOne(TransactionEvent::class);
    }

    public function reproductionEvent()
    {
        return $this->hasOne(ReproductionEvent::class);
    }

    public function birthEvent()
    {
        return $this->hasOne(BirthEvent::class);
    }

    public function milkRecord()
    {
        return $this->hasOne(MilkRecord::class);
    }

    public function deathEvent()
    {
        return $this->hasOne(DeathEvent::class);
    }

    public function weightRecord()
    {
        return $this->hasOne(WeightRecord::class);
    }

    public function evidenceFiles()
    {
        return $this->hasMany(EvidenceFile::class);
    }
}
