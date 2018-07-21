<?php

namespace Tests\BuyLot;

use Tests\TestCase;
use App\Exceptions\MarketException\{
    IncorrectLotAmountException,
    BuyOwnCurrencyException,
    IncorrectPriceException,
    BuyInactiveLotException
};
use Carbon\Carbon;
use App\Service\MarketService;
use App\Repository\Contracts\TradeRepository;
use App\Repository\Contracts\LotRepository;
use App\Repository\Contracts\WalletRepository;
use App\Repository\Contracts\MoneyRepository;
use App\Repository\Contracts\UserRepository;
use App\Repository\Contracts\CurrencyRepository;
use App\Service\Contracts\WalletService;
use App\Request\BuyLotRequest;
use App\Entity\Lot;
use App\Entity\Wallet;
use App\Entity\Money;
use App\Entity\Trade;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\TradeAdded;

class BuyLotTest extends TestCase
{
    private $marketService;
    private $tradeRepository;
    private $lotRepository;
    private $walletRepository;
    private $moneyRepository;
    private $userRepository;
    private $walletService;
   

    protected function setUp()
    {
        parent::setUp();

        $this->tradeRepository = $this->createMock(TradeRepository::class);
        $this->lotRepository = $this->createMock(LotRepository::class);
        $this->walletRepository = $this->createMock(WalletRepository::class);
        $this->moneyRepository = $this->createMock(MoneyRepository::class); 
        $this->walletService = $this->createMock(WalletService::class); 
        $this->userRepository = $this->createMock(UserRepository::class);
       
        $this->moneyRepository->expects($this->any())->method('findByWalletAndCurrency')
            ->will($this->returnCallback(function($walletId,$currencyId) {
                return new Money([
                    'wallet_id' => $walletId,
                    'currency_id' => $currencyId,
                    'amount' => 600
                ]);
            }));
        
        $this->marketService = new MarketService(
                $this->lotRepository,$this->moneyRepository,$this->walletRepository, 
                $this->tradeRepository, $this->userRepository,$this->app->make(CurrencyRepository::class),$this->walletService);
   
    }
   
    public function test_buy_lot()
    { 
        Mail::fake();
        
        $lotRequest = new BuyLotRequest(2, 1, 600);
        
        $lot =  new Lot();
        $lot->id = $lotRequest->getLotId();
        $lot->currency_id  = 1;
        $lot->seller_id = 1;
        $lot->date_time_open = Carbon::now()->toDateTimeString();
        $lot->date_time_close = Carbon::tomorrow()->toDateTimeString();
        $lot->price = 400;
        
        
        $this->lotRepository->method('getById')
             ->willReturn($lot);
       
        $this->stubGetWalletMethods($lot->seller_id,$lotRequest->getUserId());
        
        $sellerWallet = $this->walletRepository->findByUser($lot->seller_id);
        $userWallet = $this->walletRepository->findByUser($lotRequest->getUserId());
        
        
        $this->tradeRepository->method('add')
            ->will($this->returnCallback(function($trade) {
                return $trade;
            }));
        
        $dataMapMoney = array(
            array($sellerWallet->id, $lot->currency_id, self::getMoney($sellerWallet->id, $lot->currency_id, 400)),
            array($userWallet->id, $lot->currency_id, self::getMoney($userWallet->id, $lot->currency_id, 700))
        );
        
        $this->moneyRepository->expects($this->any())
            ->method('findByWalletAndCurrency')
            ->will($this->returnValueMap($dataMapMoney));
        
        
        $sellerMoney = $this->moneyRepository->findByWalletAndCurrency($sellerWallet->id,$lot->currency_id);
        $userMoney = $this->moneyRepository->findByWalletAndCurrency($userWallet->id,$lot->currency_id);
        
        
        $this->walletService->method('addMoney')->willReturn(new Money());
        $this->walletService->method('takeMoney')->willReturn(new Money());
        
        $this->userRepository->method('getById')
                ->will($this->returnCallback(function($user_id) {
                    return new User([
                        'id' => $user_id,
                        'name' => 'Inkognito',
                        'email' => 'vkolia@mail.ua',
                        'password' => ''
                    ]);
        }));
        
        $this->assertInstanceOf(Trade::class, $this->marketService->buyLot($lotRequest));
        
        Mail::assertSent(TradeAdded::class, 1);
        
    }
    public function test_buy_lot_with_own_currency()
    {
        $lotRequest = new BuyLotRequest(1, 1, 600);
        
        $lot =  new Lot();
        $lot->id = $lotRequest->getLotId();
        $lot->currency_id  = 1;
        $lot->seller_id = 1;
        $lot->date_time_open = Carbon::now()->toDateTimeString();
        $lot->date_time_close = Carbon::tomorrow()->toDateTimeString();
        $lot->price = 400;
        
         
        $this->lotRepository->method('getById')
             ->willReturn($lot);
        
        $this->stubGetWalletMethods($lot->seller_id,$lotRequest->getUserId());
       
        $this->expectException(BuyOwnCurrencyException::class);
        
        $this->marketService->buyLot($lotRequest);
         
    }
    public function test_buy_more_currency_than_lot_contains()
    {
        $lotRequest = new BuyLotRequest(2, 1, 800);
        
        $lot =  new Lot();
        $lot->id = $lotRequest->getLotId();
        $lot->currency_id  = 1;
        $lot->seller_id = 1;
        $lot->date_time_open = Carbon::now()->toDateTimeString();
        $lot->date_time_close = Carbon::tomorrow()->toDateTimeString();
        $lot->price = 400;
        
          
        $this->lotRepository->method('getById')
             ->willReturn($lot);
        
        $this->stubGetWalletMethods($lot->seller_id,$lotRequest->getUserId());
       
        $this->expectException(IncorrectLotAmountException::class);
        
        $this->marketService->buyLot($lotRequest);
         
    }
    public function test_buy_less_than_one_currency()
    {
        $lotRequest = new BuyLotRequest(2, 1, 0.4);
       
        $lot =  new Lot();
        $lot->id = $lotRequest->getLotId();
        $lot->currency_id  = 1;
        $lot->seller_id = 1;
        $lot->date_time_open = Carbon::now()->toDateTimeString();
        $lot->date_time_close = Carbon::tomorrow()->toDateTimeString();
        $lot->price = 400;
        
        
        $this->lotRepository->method('getById')
             ->willReturn($lot);
        
        $this->stubGetWalletMethods($lot->seller_id,$lotRequest->getUserId());
        
        $this->expectException(IncorrectPriceException::class);
        
        $this->marketService->buyLot($lotRequest);
         
    }
    public function test_buy_currency_from_closed_lot()
    {
        $lotRequest = new BuyLotRequest(2, 1, 400);
       
        $lot =  new Lot();
        $lot->id = $lotRequest->getLotId();
        $lot->currency_id  = 1;
        $lot->seller_id = 1;
        $lot->date_time_open = Carbon::yesterday()->subDays(4)->toDateTimeString();
        $lot->date_time_close = Carbon::yesterday()->toDateTimeString();
        $lot->price = 400;
        
        
        $this->lotRepository->method('getById')
             ->willReturn($lot);
        
        $this->stubGetWalletMethods($lot->seller_id,$lotRequest->getUserId());
        
        $this->expectException(BuyInactiveLotException::class);
        
        $this->marketService->buyLot($lotRequest);
         
    }
    public function stubGetWalletMethods($sellerId,$userId)
    {
        $dataMapWallet = array(
            array($sellerId, self::getWallet(1, $sellerId)),
            array($userId, self::getWallet(2, $userId))
        );
        
        $this->walletRepository->expects($this->any())
            ->method('findByUser')
            ->will($this->returnValueMap($dataMapWallet));
    }
    public static function getWallet($wallet_id,$user_id) {
        
        $wallet = new Wallet();
        $wallet->id = $wallet_id;
        $wallet->user_id = $user_id;
    
        return $wallet;
    }
    private static function getMoney($wallet_id,$currency_id,$amount) {
        $money = new Money();
        $money->wallet_id = $wallet_id;
        $money->currency_id = $currency_id;
        $money->amount = $amount;
        return $money;
    }
  
 
   
}
