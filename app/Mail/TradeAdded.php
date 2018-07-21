<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use App\Entity\Trade;
 
class TradeAdded extends Mailable
{
    public $userName;
    public $trade;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userName,Trade $trade)
    {
        $this->userName = $userName;
        $this->trade = $trade;
    }

     /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
    
        return $this->view('emails.trade_created')
            ->with([
                'userName' => $this->userName,
                'trade' => $this->trade,
            ]);
    }
}
 
