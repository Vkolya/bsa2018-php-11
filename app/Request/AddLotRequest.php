<?php

namespace App\Request;

use App\Request\Contracts\AddLotRequest as AddLotRequestInterface; 

class AddLotRequest implements AddLotRequestInterface
{
    private $currencyId;
    private $sellerId;
    private $dateTimeOpen;
    private $dateTimeClose;
    private $price;
    
    public function __construct($currencyId,$sellerId,$dateTimeOpen,$dateTimeClose,$price) 
    {
        $this->currencyId = $currencyId;
        $this->sellerId = $sellerId;  
        $this->dateTimeOpen = $dateTimeOpen;
        $this->dateTimeClose = $dateTimeClose;
        $this->price = $price;
    }
    
    public function getCurrencyId() : int
    {
        return $this->currencyId;
    }

    /**
     * An identifier of user
     *
     * @return int
    */
    public function getSellerId() : int
    {
        return $this->sellerId;
    }
    /**
     * Timestamp
     *
     * @return int
     */
    public function getDateTimeOpen() : int
    {
         return strtotime($this->dateTimeOpen);
    }

    /**
     * Timestamp
     *
     * @return int
     */
    public function getDateTimeClose() : int
    {
        return strtotime($this->dateTimeClose);
    }

    public function getPrice() : float
    {
        return $this->price;
    }
}