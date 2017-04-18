<?php

namespace bitrefill\response;

use bitrefill\base\Object;

class Order extends Object
{
    public $btcPrice;
    public $eurPrice;
    public $usdPrice;
    public $itemDesc;
    public $invoiceTime;
    public $expirationTime;
    public $orderId;

    /**
     * @var Payment
     */
    public $payment;

    public function getOrderInfo()
    {

    }
}