<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddLotRequest;
use App\Repository\Contracts\CurrencyRepository;

class LotController extends Controller
{
    private $currencyRepo;

    public function __construct(CurrencyRepository $currencyRepo) {
        $this->currencyRepo = $currencyRepo;
    }
    
    public function add()
    {
        return view('add_lot');
    }
    public function store(AddLotRequest $request)
    {
        
        $currency = $this->currencyRepo->getCurrencyByName($request['currency']);
         
        if ($currency) {
            $message = 'Lot has been added successfully!';
        }else {
            $message = 'Sorry, error has been occurred: Currency doesnt exists!';
        }
        return view('add_lot', compact('message'));

    }
}
