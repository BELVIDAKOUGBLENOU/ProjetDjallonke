<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    /** @use HasFactory<\Database\Factories\PersonFactory> */
    use HasFactory;

    protected $table = 'persons';

    protected $fillable = ['name', 'address', 'phone', 'nationalId'];

    public function premisesKeepers()
    {
        return $this->hasMany(PremisesKeeper::class);
    }

    public function personRoles()
    {
        return $this->hasMany(PersonRole::class);
    }

    public function transactionPurchases()
    {
        return $this->hasMany(TransactionEvent::class, 'buyer_id');
    }

    public function transactionSales()
    {
        return $this->hasMany(TransactionEvent::class, 'seller_id');
    }
}
