<?php

namespace Tests\Feature;

 
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Tests\TestCase;
use App\User;
use App\Entity\Lot;
use App\Entity\Money;

class LotTest extends TestCase
{
    use RefreshDatabase;

    /*
     * ADD LOT TESTS
     */
    public function test_add_lot()
    {
         
        $user = factory(User::class)->make(['id' => 1]);
        
        $response = $this->actingAs($user)->json('POST', '/api/v1/lots', [
            "currency_id" => 1,
            "date_time_open"=> Carbon::now()->toDateTimeString(),
            "date_time_close"=> Carbon::tomorrow()->toDateTimeString(),
            "price"=> 3
        ]);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(201);
 
    }
    public function test_add_lot_with_same_currency()
    {
         
        $user = factory(User::class)->make(['id' => 1]);
        factory(Money::class,2)->create(['amount' => 300]);
        $lot = factory(Lot::class)->create(['currency_id' => 1,'seller_id' => $user->id]); 
        
        $response = $this->actingAs($user)->json('POST', '/api/v1/lots', [
            "currency_id" => 1,
            "date_time_open"=> Carbon::now()->toDateTimeString(),
            "date_time_close"=> Carbon::tomorrow()->toDateTimeString(),
            "price"=> 3
        ]);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(400);
 
    }
    public function test_add_lot_with_date_open_greater_than_date_close()
    {
        $user = factory(User::class)->make(['id' => 1]);
         
        $response = $this->actingAs($user)->json('POST', '/api/v1/lots', [
            "currency_id" => 1,
            "date_time_open"=> Carbon::tomorrow()->toDateTimeString(),
            "date_time_close"=> Carbon::now()->toDateTimeString(),
            "price"=> 3
        ]);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(400);
    }
    public function test_add_lot_with_negative_amount()
    {
        $user = factory(User::class)->make(['id' => 1]);
         
        $response = $this->actingAs($user)->json('POST', '/api/v1/lots', [
            "currency_id" => 1,
            "date_time_open"=> Carbon::tomorrow()->toDateTimeString(),
            "date_time_close"=> Carbon::now()->toDateTimeString(),
            "price"=> -3
        ]);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(400);
    }
    public function test_add_lot_unauthorized_user()
    {
        
        $response = $this->json('POST', '/api/v1/lots', [
            "currency_id" => 1,
            "date_time_open"=> Carbon::now()->toDateTimeString(),
            "date_time_close"=> Carbon::tomorrow()->toDateTimeString(),
            "price"=> 3
        ]);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(403);
 
    }
    /*
     * BUY LOT TESTS
     */
    public function test_buy_lot()
    {
        factory(Money::class,2)->create(['amount' => 300]);
        
        $seller = User::all()[0];
        $user = User::all()[1];
       
        $lot = factory(Lot::class)->create(['currency_id' => $seller->wallet->currencies()->first()->id,'seller_id' => $seller->id]); 
        
        $response = $this->actingAs($user)->json('POST', '/api/v1/trades', [
            "lot_id" => $lot->id,
            "amount" => 300
        ]);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(201);
 
    }
    public function test_buy_lot_with_own_currency()
    {
        factory(Money::class)->create(['amount' => 300]);
        
        $seller = User::all()[0];
       
        $lot = factory(Lot::class)->create(['currency_id' => $seller->wallet->currencies()->first()->id,'seller_id' => $seller->id]); 
        
        $response = $this->actingAs($seller)->json('POST', '/api/v1/trades', [
            "lot_id" => $lot->id,
            "amount" => 300
        ]);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(400);
    }
    public function test_buy_more_currency_than_lot_contains()
    {
        factory(Money::class,2)->create(['amount' => 300]);
        
        $seller = User::all()[0];
        $user = User::all()[1];
       
        $lot = factory(Lot::class)->create(['currency_id' => $seller->wallet->currencies()->first()->id,'seller_id' => $seller->id]); 
        
        $response = $this->actingAs($user)->json('POST', '/api/v1/trades', [
            "lot_id" => $lot->id,
            "amount" => 600
        ]);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(400);
    }
    public function test_buy_less_than_one_currency()
    {
        factory(Money::class,2)->create(['amount' => 300]);
        
        $seller = User::all()[0];
        $user = User::all()[1];
       
        $lot = factory(Lot::class)->create(['currency_id' => $seller->wallet->currencies()->first()->id,'seller_id' => $seller->id]); 
        
        $response = $this->actingAs($user)->json('POST', '/api/v1/trades', [
            "lot_id" => $lot->id,
            "amount" => 0.4
        ]);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(400);
    }
    public function test_buy_currency_from_closed_lot()
    {
        factory(Money::class,2)->create(['amount' => 300]);
        
        $seller = User::all()[0];
        $user = User::all()[1];
       
        $lot = factory(Lot::class)->create(['currency_id' => $seller->wallet->currencies()->first()->id,'seller_id' => $seller->id,'date_time_close' => Carbon::createFromTimestamp((int) time()-3600)]); 
        
        $response = $this->actingAs($user)->json('POST', '/api/v1/trades', [
            "lot_id" => $lot->id,
            "amount" => 0.4
        ]);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(400);
    }
    public function test_buy_lot_unauthorized_user()
    {
        factory(Money::class,2)->create(['amount' => 300]);
        
        $seller = User::all()[0];
        $user = User::all()[1];
       
        $lot = factory(Lot::class)->create(['currency_id' => $seller->wallet->currencies()->first()->id,'seller_id' => $seller->id]); 
        
        $response = $this->json('POST', '/api/v1/trades', [
            "lot_id" => $lot->id,
            "amount" => 300
        ]);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(403);
 
    }
    public function test_view_lot()
    {
        
        factory(Money::class)->create(['amount' => 300]);
        $seller = User::all()->first();
        
        $lot = factory(Lot::class)->create(['currency_id' => $seller->wallet->currencies()->first()->id,'seller_id' => $seller->id]); 
        
        $response = $this->actingAs($seller)->json('GET', '/api/v1/lots/'.$lot->id);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'user_name',
            'currency_name',
            'amount',
            'date_time_open',
            'date_time_close',
            'price'
        ]);
 
    }
    public function test_view_lot_list()
    {
         
        factory(Money::class,10)->create();
        $users = User::all();
         
        foreach ($users as $user) {
            factory(Lot::class)->create(['currency_id' => $user->wallet->currencies()->first()->id,'seller_id' => $user->id]); 
        }
       
        $response = $this->actingAs($users->first())->json('GET', '/api/v1/lots');
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'user_name',
                'currency_name',
                'amount',
                'date_time_open',
                'date_time_close',
                'price'
            ]
        ]);
      
        
    }
}