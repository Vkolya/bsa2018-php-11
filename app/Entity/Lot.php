<?php

namespace App\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    protected $fillable = [
        "id",
        'currency_id',
        'seller_id',
        'date_time_open',
        'date_time_close',
        'price'
    ];

    public function getDateTimeOpen() : int
    {
        if (is_int($this->date_time_open)) {
            return $this->date_time_open;
        } else {
            return (new Carbon($this->date_time_open))->getTimestamp();
        }
    }

    public function getDateTimeClose() : int
    {
        if (is_int($this->date_time_close)) {
            return $this->date_time_close;
        } else {
            return (new Carbon($this->date_time_close))->getTimestamp();
        }
    }
    /**
     * Get lot's currency
     */
    public function currency()
    {
        return $this->hasOne(Currency::class);
    }
    /**
     * Get lot's seller
     */
    public function seller()
    {
        return $this->hasOne(User::class);
    }
    public function scopeActive($query)
    {
        return $query->where('date_time_close','>',Carbon::now());
    }
}
