<?php

namespace App\Service;

use App\Entity\Money;
use App\Entity\Wallet;
use App\Request\Contracts\CreateWalletRequest;
use App\Request\Contracts\MoneyRequest;
use App\Repository\Contracts\MoneyRepository;
use App\Repository\Contracts\WalletRepository;
use App\Service\Contracts\WalletService as WalletServiceInterface;

class WalletService implements WalletServiceInterface
{
    /**
     * @var \App\Repository\Contracts\MoneyRepository
    */
    private $moneyRepository;
    /**
     * @var \App\Repository\Contracts\WalletRepository
    */
    private $walletRepository;


    public function __construct(MoneyRepository $moneyRepository,WalletRepository $walletRepository) {
        $this->moneyRepository = $moneyRepository;
        $this->walletRepository = $walletRepository;
    }
    
    /**
     * Add wallet to user.
     *
     * @param CreateWalletRequest $walletRequest
     * @return Wallet
     */
    public function addWallet(CreateWalletRequest $walletRequest) : Wallet
    {
        $wallet = new Wallet();
        $wallet->user_id = $walletRequest->getUserId();
        
        return $this->walletRepository->add($wallet);
    }

    /**
     * Add money to a wallet.
     *
     * @return Money
     */
    public function addMoney(MoneyRequest $moneyRequest) : Money
    {
        $money = $this->moneyRepository->findByWalletAndCurrency($moneyRequest->getWalletId(),$moneyRequest->getCurrencyId());
       
        if (empty($money)) {
            $money = new Money([
                'wallet_id' => $moneyRequest->getWalletId(),
                'currency_id' => $moneyRequest->getCurrencyId(),
                'amount' => $moneyRequest->getAmount(),
            ]);
        }
        $money->amount += $moneyRequest->getAmount();
        
        return $this->moneyRepository->save($money);
    }

    /**
     * Take money from a wallet.
     *
     * @param MoneyRequest $currencyRequest
     * @return Money
     */
    public function takeMoney(MoneyRequest $moneyRequest) : Money
    {
        $money = $this->moneyRepository->findByWalletAndCurrency($moneyRequest->getWalletId(),$moneyRequest->getCurrencyId());
       
        if ($money->amount >= $moneyRequest->getAmount()) {
            $money->amount -= $moneyRequest->getAmount();
        }
        return $money;
    }
}