<?php

namespace App\Service;

use App\Entity\Currency;
use App\Request\Contracts\AddCurrencyRequest;
use App\Repository\Contracts\CurrencyRepository;
use App\Service\Contracts\CurrencyService as CurrencyServiceInterface;

class CurrencyService implements CurrencyServiceInterface
{
    private $currencyRepo;
    
    public function __construct(CurrencyRepository $currencyRepo)
    {
        $this->currencyRepo = $currencyRepo;
    }
    public function addCurrency(AddCurrencyRequest $currencyRequest): Currency
    {
        $currency = new Currency;
        $currency->fill(['name' => $currencyRequest->getName()]); 
        return $this->$currencyRepo->add($currency);
    }
}
