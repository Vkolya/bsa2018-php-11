<?php

namespace App\Request;

use App\Request\Contracts\BuyLotRequest as BuyLotRequestInterface;

class BuyLotRequest implements BuyLotRequestInterface
{
    private $userId;
    private $lotId;
    private $amount;
    
    public function __construct($userId,$lotId,$amount)
    {
        $this->userId  =$userId;
        $this->lotId = $lotId;
        $this->amount = $amount;
    }
    
    public function getUserId() : int
    {
        return $this->userId;
    }

    public function getLotId() : int
    {
        return $this->lotId;
    }

    public function getAmount() : float
    {
        return $this->amount;
    }
}