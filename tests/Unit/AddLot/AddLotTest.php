<?php

namespace Tests\AddTest;

use Tests\TestCase;
use App\Exceptions\MarketException\{
    ActiveLotExistsException,
    IncorrectTimeCloseException,
    BuyNegativeAmountException
};
use Carbon\Carbon;
use App\Service\MarketService;
use App\Repository\Contracts\LotRepository;
use App\Repository\Contracts\WalletRepository;
use App\Repository\Contracts\MoneyRepository;
use App\Repository\Contracts\TradeRepository;
use App\Repository\Contracts\UserRepository;
use App\Repository\Contracts\CurrencyRepository;
use App\Service\Contracts\WalletService;
use App\Request\AddLotRequest;
use App\Entity\Lot;

class AddLotTest extends TestCase
{
    private $marketService;
    private $lotRepository;


    protected function setUp()
    {
        parent::setUp();

        $this->lotRepository = $this->createMock(LotRepository::class);
        
        $this->marketService = new MarketService(
                $this->lotRepository,$this->app->make(MoneyRepository::class),$this->app->make(WalletRepository::class),
                $this->app->make(TradeRepository::class),$this->app->make(UserRepository::class),
                $this->app->make(CurrencyRepository::class),$this->app->make(WalletService::class)
        );
        
    }
   
    public function test_add_lot()
    { 
        $this->lotRepository->method('add')
             ->will($this->returnCallback(function($lot) {
                   return $lot;
             }));
      
        $this->lotRepository->method('findActiveLotByCurrency')
             ->willReturn(null);
             
        $lot_request = new AddLotRequest(
                1,
                1,
                Carbon::now(),
                Carbon::tomorrow(),
                400
        );
    
        $this->assertInstanceOf(Lot::class, $this->marketService->addLot($lot_request));
         
    }
    public function test_add_lot_with_same_currency()
    {
        $lot =  new Lot();
        
        $lot->currency_id  = 1;
        $lot->seller_id = 1;
        $lot->date_time_open = Carbon::now()->toDateTimeString();
        $lot->date_time_close = Carbon::tomorrow()->toDateTimeString();
        $lot->price = 400;
        
        
        $this->lotRepository->method('findActiveLotByCurrency')
             ->willReturn($lot);
        
        $lotRequest = new AddLotRequest(1, 1, Carbon::now(), Carbon::tomorrow(), 400);
        
        $this->expectException(ActiveLotExistsException::class);
        
        $this->marketService->addLot($lotRequest);
        
    }
    public function test_add_lot_with_date_open_greater_than_date_close()
    {
        $lotRequest = new AddLotRequest(
                    1,
                    1,
                    Carbon::tomorrow()->toDateTimeString(),
                    Carbon::now()->toDateTimeString(),
                    400
        );
        
        $this->expectException(IncorrectTimeCloseException::class);
        
        $this->marketService->addLot($lotRequest);
        
    }
    public function test_add_lot_with_negative_amount()
    {
        $lotRequest = new AddLotRequest(
                    1,
                    1,
                    Carbon::now()->toDateTimeString(),
                    Carbon::tomorrow()->toDateTimeString(),
                    -400
        );
        
        $this->expectException(BuyNegativeAmountException::class);
        
        $this->marketService->addLot($lotRequest);
        
    }
   
}
