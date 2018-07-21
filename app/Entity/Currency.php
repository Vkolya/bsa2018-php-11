<?php

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        "id",
        "name"
    ];
    /**
     * Get money with currency
    */
    public function money()
    {
        return $this->hasMany(Money::class);
    }
}
