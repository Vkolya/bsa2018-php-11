<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Service\Contracts\MarketService as MarketServiceInterface;
use App\Repository\Contracts\LotRepository as LotRepositoryInterface;
use App\Repository\Contracts\TradeRepository as TradeRepositoryInterface;
use App\Repository\Contracts\WalletRepository as WalletRepositoryInterface;
use App\Repository\Contracts\MoneyRepository as MoneyRepositoryInterface;
use App\Repository\Contracts\UserRepository as UserRepositoryInterface;
use App\Repository\Contracts\CurrencyRepository as CurrencyRepositoryInterface;
use App\Service\Contracts\WalletService as WalletServiceInterface;
use App\Service\MarketService;
use App\Repository\LotRepository;
use App\Repository\TradeRepository;
use App\Repository\WalletRepository;
use App\Repository\MoneyRepository;
use App\Repository\UserRepository;
use App\Repository\CurrencyRepository;
use App\Service\WalletService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
        $this->app->bind(MarketServiceInterface::class, MarketService::class);
        $this->app->bind(WalletServiceInterface::class, WalletService::class);
        $this->app->bind(LotRepositoryInterface::class, LotRepository::class);
        $this->app->bind(TradeRepositoryInterface::class, TradeRepository::class);
        $this->app->bind(WalletRepositoryInterface::class, WalletRepository::class);
        $this->app->bind(MoneyRepositoryInterface::class, MoneyRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CurrencyRepositoryInterface::class, CurrencyRepository::class);
        
    }
}
