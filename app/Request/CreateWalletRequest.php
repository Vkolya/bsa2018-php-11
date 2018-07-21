<?php

namespace App\Request;
use App\Request\Contracts\CreateWalletRequest as CreateWalletRequestInterface;
class CreateWalletRequest implements CreateWalletRequestInterface
{
    private $userId;
    
    public function __construct(int $userId) {
        $this->userId = $userId;
    }
    
    public function getUserId() : int
    {
        return $this->userId;
    }
}
