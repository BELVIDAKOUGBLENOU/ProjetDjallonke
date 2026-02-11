<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionEvent extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionEventFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'transaction_type',
        'price',
        'buyer_id',
        'seller_id',
    ];
    protected $casts = [
        'price' => 'float'
    ];
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function buyer()
    {
        return $this->belongsTo(Person::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(Person::class, 'seller_id');
    }
    public static function getTableName()
    {
        return (new self)->getTable();
    }
}
