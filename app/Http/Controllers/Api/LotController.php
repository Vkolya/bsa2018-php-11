<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Request\AddLotRequest;
use Illuminate\Support\Facades\Auth;
use App\Service\Contracts\MarketService;

class LotController extends Controller
{
    private $markerService;
    
    public function __construct(MarketService $markerService) {
        $this->markerService = $markerService;
    }
    public function index()
    {
        $lots = $this->markerService->getLotList();
        return response()->json($lots, 200);
    }
    public function addLot(Request $request)
    {
        if(!Auth::user()) {
            return response()->json('Forbidden', 403);
        }
        $lotRequest = new AddLotRequest($request['currency_id'], Auth::user()->id,$request['date_time_open'],$request['date_time_close'],$request['price']);
        try {
            $lot = $this->markerService->addLot($lotRequest);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
        
        return response()->json(['lot' => $lot], 201);
    
    }
    public function show(int $id) 
    {
        try {
            $lot = $this->markerService->getLot($id);
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
        return response()->json($lot, 200); 
    }
}
