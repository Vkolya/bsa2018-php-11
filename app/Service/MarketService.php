<?php

namespace App\Service;

use App\Service\Contracts\MarketService as MarketServiceInterface;
use App\Repository\Contracts\LotRepository as LotRepositoryInterface;
use App\Repository\Contracts\MoneyRepository as MoneyRepositoryInterface;
use App\Repository\Contracts\WalletRepository as WalletRepositoryInterface;
use App\Repository\Contracts\TradeRepository as TradeRepositoryInterface;
use App\Service\Contracts\WalletService as WalletServiceInterface;
use App\Repository\Contracts\UserRepository as UserRepositoryInterface;
use App\Repository\Contracts\CurrencyRepository as CurrencyRepositoryInterface;
use App\Entity\{ Lot, Trade};
use App\Request\Contracts\{ AddLotRequest, BuyLotRequest };
use App\Response\Contracts\LotResponse;
use App\Exceptions\MarketException\{
    ActiveLotExistsException,
    IncorrectPriceException,
    IncorrectTimeCloseException,
    BuyOwnCurrencyException,
    IncorrectLotAmountException,
    BuyNegativeAmountException,
    BuyInactiveLotException,
    LotDoesNotExistException
};
use Carbon\Carbon;
use App\Request\MoneyRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\TradeAdded;
 
class MarketService implements MarketServiceInterface
{
    /**
     * @var \App\Repository\Contracts\LotRepository
    */
    private $lotRepo;
    /**
     * @var \App\Repository\Contracts\MoneyRepository
    */
    private $moneyRepo;
    /**
     * @var \App\Repository\Contracts\WalletRepository
    */
    private $walletRepo;
    /**
     * @var \App\Repository\Contracts\TradeRepository
    */
    private $tradeRepo;
    /**
     * @var \App\Repository\Contracts\UserRepository
    */
    private $userRepo;
    /**
     * @var \App\Repository\Contracts\CurrencyRepository
    */
    private $currencyRepo;
    /**
     * @var \App\Service\Contracts\WalletService
    */
    private $walletService;
    
    public function __construct(
            LotRepositoryInterface $lotRepo,
            MoneyRepositoryInterface $moneyRepo,
            WalletRepositoryInterface $walletRepo,
            TradeRepositoryInterface $tradeRepo,
            UserRepositoryInterface $userRepo,
            CurrencyRepositoryInterface $currencyRepo,
            WalletServiceInterface $walletService) 
    {
        $this->lotRepo = $lotRepo;
        $this->moneyRepo = $moneyRepo;
        $this->walletRepo = $walletRepo;
        $this->tradeRepo = $tradeRepo;
        $this->userRepo = $userRepo;
        $this->currencyRepo = $currencyRepo;
        $this->walletService = $walletService;
    }
    /**
     * Add lot with currency.
     *
     * @param AddLotRequest $lotRequest
     * 
     * @throws ActiveLotExistsException
     * @throws IncorrectTimeCloseException
     * @throws BuyNegativeAmountException
     *
     * @return Lot
     */
    public function addLot(AddLotRequest $lotRequest) : Lot
    {
       
        if($this->lotRepo->findActiveLotByCurrency($lotRequest->getSellerId(),$lotRequest->getCurrencyId())) {
            throw  new ActiveLotExistsException("User already have an active lot with this currency!");
        }
        if($lotRequest->getDateTimeOpen() > $lotRequest->getDateTimeClose()) {
            throw new IncorrectTimeCloseException('Time close must be greater than time open!');
        }
        if($lotRequest->getPrice() < 0) {
            throw new BuyNegativeAmountException('Amount cant be negative!');
        }
        
        $lot = new Lot([
            'currency_id' => $lotRequest->getCurrencyId(),
            'seller_id' => $lotRequest->getSellerId(),
            'date_time_open' => $lotRequest->getDateTimeOpen(),
            'date_time_close' => $lotRequest->getDateTimeClose(),
            'price' => $lotRequest->getPrice(),
        ]);
      
        $this->lotRepo->add($lot);
     
        return $lot;
    }
    /**
     * Buy currency.
     *
     * @param BuyLotRequest $lotRequest
     * 
     * @throws BuyOwnCurrencyException
     * @throws IncorrectLotAmountException
     * @throws BuyNegativeAmountException
     * @throws BuyInactiveLotException
     * 
     * @return Trade
     */
    public function buyLot(BuyLotRequest $lotRequest) : Trade
    {
        
        if($lot = $this->lotRepo->getById($lotRequest->getLotId())) {
            
            $sellerWallet = $this->walletRepo->findByUser($lot->seller_id);
     
            if($lot->seller_id == $lotRequest->getUserId()) {
                throw  new BuyOwnCurrencyException("You cant buy own currency!");
            }
            
            if($lotRequest->getAmount() > $this->moneyRepo->findByWalletAndCurrency($sellerWallet->id,$lot->currency_id)->amount) {
                throw new IncorrectLotAmountException('Currency amount cant be greater than lot price!');
            }
            if($lotRequest->getAmount() < 1) {
                throw new IncorrectPriceException('Currency amount must be  => 1!');
            }
            if($lot->date_time_close < Carbon::now()->toDateTimeString()) {
                throw new BuyInactiveLotException('Lot is inactive!');
            }
          
            $trade = new Trade();
            $trade->lot_id = $lot->id;
            $trade->user_id = $lotRequest->getUserId();
            $trade->amount = $lotRequest->getAmount();
            $this->tradeRepo->add($trade);
     
            $userWallet = $this->walletRepo->findByUser($lotRequest->getUserId());
            
            $this->walletService->addMoney(new MoneyRequest($userWallet->id, $lot->currency_id,$lotRequest->getAmount()));
            $this->walletService->takeMoney(new MoneyRequest($sellerWallet->id, $lot->currency_id,$lotRequest->getAmount()));
            
            $seller = $this->userRepo->getById($lot->seller_id);
         
            Mail::to($seller->email)->send(new TradeAdded($seller->name,$trade));
          
            return $trade;
            
        }else {
            throw new LotDoesNotExistException("Lot doesnt exists!");
        }
        
    }

    /**
     * Retrieves lot by an identifier and returns it in LotResponse format
     *
     * @param int $id
     * 
     * @throws LotDoesNotExistException
     * 
     * @return LotResponse
     */
 
    public function getLot(int $id) : LotResponse
    {
        $lot = $this->lotRepo->getById($id);
        if ($lot) {
            $currency = $this->currencyRepo->getById($lot->currency_id);
            $user = $this->userRepo->getById($lot->seller_id);
            $wallet = $this->walletRepo->findByUser($lot->seller_id);
            $money = $this->moneyRepo->findByWalletAndCurrency($wallet->id, $currency->id);
            $response = new \App\Response\LotResponse($lot->id, $user->name, $currency->name, $money->amount, $lot->getDateTimeOpen(), $lot->getDateTimeClose(), $lot->price);

            return $response;
        }else {
            throw new LotDoesNotExistException("Lot doesnt exists!");
        }
    }
     /**
     * Return list of lots.
     *
     * @return LotResponse[]
     */
    public function getLotList() : array
    {
        $lotList = [];
        $lots = $this->lotRepo->findAll();
        foreach ($lots as $lot) {
            $lotList[] = $this->getLot($lot->id);
        }
        return $lotList;
    }
}
