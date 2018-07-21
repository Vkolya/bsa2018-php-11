<?php

namespace App\Repository;

use App\Entity\Trade;
use App\Repository\Contracts\TradeRepository as TradeRepositoryInterface;

class TradeRepository implements TradeRepositoryInterface
{
    /**
     * @inheritdoc
     */ 
    public function add(Trade $trade) : Trade
    {
        $trade->save();
        
        return $trade;
    }
}