<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvidenceFile extends Model
{
    /** @use HasFactory<\Database\Factories\EvidenceFileFactory> */
    use HasFactory;

    protected $fillable = ['uid', 'event_id', 'url', 'file_type'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
