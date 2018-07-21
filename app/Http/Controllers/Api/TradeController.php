<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Request\BuyLotRequest;
use Illuminate\Support\Facades\Auth;
use App\Service\Contracts\MarketService;

class TradeController extends Controller
{
    private $markerService;
    
    public function __construct(MarketService $markerService) {
        $this->markerService = $markerService;
    }
    public function createTrade(Request $request)
    {
        if(!Auth::user()) {
            return response()->json('Forbidden', 403);
        }
        $lotRequest = new BuyLotRequest(Auth::user()->id,$request['lot_id'],$request['amount']);
        try {
            $trade = $this->markerService->buyLot($lotRequest);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
        
        return response()->json(['trade' => $trade], 201);
        
    }
}
