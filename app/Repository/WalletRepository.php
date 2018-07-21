<?php

namespace App\Repository;

use App\Entity\Wallet;
use App\Repository\Contracts\WalletRepository as WalletRepositoryInterface;

class WalletRepository implements WalletRepositoryInterface
{
    /**
     * @inheritdoc
     */ 
    public function add(Wallet $wallet) : Wallet
    {
        
    }
    /**
     * @inheritdoc
     */ 
    public function findByUser(int $userId) : ?Wallet
    {
        return Wallet::where('user_id',$userId)->first();
    }
}