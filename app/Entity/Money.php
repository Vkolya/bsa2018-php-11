<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class Money extends Model
{
    protected $fillable = [
        "wallet_id",
        "currency_id",
        "amount"
    ];
    /**
     * Get wallets
    */
    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }
    /**
     * Get the money currency
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
