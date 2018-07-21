<?php

namespace App\Repository;

use App\Entity\Currency;
use App\Repository\Contracts\CurrencyRepository as CurrencyRepositoryInterface;

class CurrencyRepository implements CurrencyRepositoryInterface
{
    /**
     * @inheritdoc
     */ 
    public function add(Currency $currency) : Currency
    {
        $currency->save();
        return $currency;
    }
    /**
     * @inheritdoc
     */ 
    public function getById(int $id) : ?Currency
    {
        return Currency::find($id);
    }
    /**
     * @inheritdoc
     */     
    public function getCurrencyByName(string $name) : ?Currency
    {
        return Currency::where('name',$name)->first();
    }

    /**
     * @return Currency[]
     */
    public function findAll()
    {
        return Currency::all();
    }
}
