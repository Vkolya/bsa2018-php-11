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
use App\Response\Contracts\LotResponse;
use App\Entity\Lot;
use App\Entity\Currency;
use App\User;
use App\Entity\Wallet;
use App\Entity\Money;
 
 
class GetLotAndLotListTest extends TestCase
{
    private $marketService;
    private $tradeRepository;
    private $lotRepository;
    private $walletRepository;
    private $moneyRepository;
    private $userRepository;
    private $currencyRepository;
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
        $this->currencyRepository = $this->createMock(CurrencyRepository::class);
        
       
        $this->marketService = new MarketService(
                $this->lotRepository,$this->moneyRepository,$this->walletRepository, 
                $this->tradeRepository, $this->userRepository,
                $this->currencyRepository,$this->walletService
        );
   
        self::setUpMockedRepositories();
        
    }
   
    public function test_get_lot()
    { 
        $lotId = 1; 
         
        $lot = $this->lotRepository->getById($lotId); 
        $wallet = $this->walletRepository->findByUser($lot->seller_id);
       
        $lotResponse = $this->marketService->getLot($lotId);
        $this->assertInstanceOf(LotResponse::class, $lotResponse);
        $this->assertEquals($lotResponse->getAmount(), $this->moneyRepository->findByWalletAndCurrency($wallet->id,$lot->currency_id)->amount);
        $this->assertRegExp('/^(\d{4})\/(\d{2})\/(\d{2}) (\d{2}):(\d{2}):(\d{2})$/', $lotResponse->getDateTimeOpen());
        $this->assertRegExp('/^(\d{4})\/(\d{2})\/(\d{2}) (\d{2}):(\d{2}):(\d{2})$/', $lotResponse->getDateTimeClose());
        $this->assertRegExp('/^(\d+),(\d{2})$/', $lotResponse->getPrice());
    }
    public function test_get_lot_list()
    { 
         
        $this->lotRepository->method('findAll')
             ->willReturn(self::getLots());
        
        $lotList = $this->marketService->getLotList();
        
        foreach ($lotList as $lot) {
            $this->assertInstanceOf(LotResponse::class,$lot);
        }
     
      
    }
    
    public static function getLots() 
    {
        return [
            new Lot([
                'id' => 1,
                'currency_id' => 1,
                'seller_id' => 1,
                'date_time_open' => Carbon::now(),
                'date_time_close' => Carbon::tomorrow(),
                'price' => 400,
            ]),
            new Lot([
                'id' => 2,
                'currency_id' => 2,
                'seller_id' => 2,
                'date_time_open' => Carbon::now(),
                'date_time_close' => Carbon::tomorrow(),
                'price' => 400,
            ])
        ];
    }
    
    protected function setUpMockedRepositories() : void
    {
        $this->lotRepository->method('getById')
            ->will($this->returnCallback(function($lotId) {
                    return new Lot([
                        'id' => $lotId,
                        'currency_id' => 1,
                        'seller_id' => 1,
                        'date_time_open' => Carbon::now(),
                        'date_time_close' => Carbon::tomorrow(),
                        'price' => 400,
                    ]);
            }));
        
        $this->currencyRepository->method('getById')
            ->will($this->returnCallback(function($currency_id) {
                    return new Currency([
                        'id' => $currency_id,
                        'name' => 'Bitcoin'
                    ]);
             }));
       
        
        $this->userRepository->method('getById')
             ->will($this->returnCallback(function($seller_id) {
                    return new User([
                        'id' => $seller_id,
                        'name' => 'Inkognito',
                        'email' => 'vkolia@mail.ua',
                        'password' => ''
                    ]);
             }));
        
        $this->walletRepository->method('findByUser')
            ->will($this->returnCallback(function($user_id) {
                    return new Wallet([
                        'id' => random_int(1, 1000), //тупа заглушка , бо не вистачило часу придумати щось оригінальніше
                        'user_id' => $user_id
                    ]);
            }));
       
        
        $this->moneyRepository->method('findByWalletAndCurrency')
            ->will($this->returnCallback(function($walletId,$currencyId) {
                    return new Money([
                        'wallet_id' => $walletId,
                        'currency_id' => $currencyId,
                        'amount'    => 400
                    ]);
            }));
        
           
 
    }
    
    
 
   
}
