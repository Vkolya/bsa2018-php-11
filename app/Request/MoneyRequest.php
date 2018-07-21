<?php

namespace App\Request;

use App\Request\Contracts\MoneyRequest as MoneyRequestInterface;

class MoneyRequest implements MoneyRequestInterface
{
    private $walletId;
    private $currencyId;
    private $amount;
    
    public function __construct($walletId,$currencyId,$amount) 
    {
        $this->walletId = $walletId;
        $this->currencyId = $currencyId;
        $this->amount = $amount;
    }
    public function getWalletId() : int
    {
        return $this->walletId;
    }

    public function getCurrencyId() : int
    {
        return $this->currencyId;
    }

    public function getAmount() : float
    {
        return $this->amount;
    }
}