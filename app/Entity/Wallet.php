<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;
use App\User;


class Wallet extends Model
{
    protected $fillable = [
        "id",
        "user_id"
    ];
    /**
     * Get the user that owns the wallet.
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get wallet's money
     */
    public function money()
    {
        return $this->hasMany(Money::class);
    }
    public function currencies()
    {
        return $this->belongsToMany(Currency::class, 'money', 'wallet_id', 'currency_id');
    }
}
