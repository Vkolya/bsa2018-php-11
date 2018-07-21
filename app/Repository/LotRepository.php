<?php

namespace App\Repository;

use App\Entity\Lot;
use App\Repository\Contracts\LotRepository as LotRepositoryInterface;

class LotRepository implements LotRepositoryInterface
{
    /**
     * @inheritdoc
     */ 
    public function add(Lot $lot) : Lot
    {
        $lot->save;
        
        return $lot;
    }
    /**
     * @inheritdoc
     */ 
    public function getById(int $id) : ?Lot
    {
        return Lot::find($id);
    }

    /**
     * @return Lot[]
     */
    public function findAll()
    {
        return Lot::all();
    }
    /**
     * @inheritdoc
     */ 
    public function findActiveLot(int $userId) : ?Lot
    {
        return Lot::active()->first()->get();
    }
    /**
     * @inheritdoc
     */ 
    public function findActiveLotByCurrency(int $userId, int $currencyId) : ?Lot
    {
        return Lot::active()->where('seller_id', $userId)
            ->where('currency_id', $currencyId)
            ->first();    
    }
}