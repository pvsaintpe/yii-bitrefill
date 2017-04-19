<?php

namespace bitrefill\response;

use bitrefill\Apiary;
use bitrefill\base\Object;

class Order extends Object
{
    public $id;
    public $email;
    public $number;
    public $paymentRecieved;
    public $sent;
    public $refunded;
    public $value;
    public $currency;
    public $operatorResponse;
    public $operator;
    public $country;
    public $summary;
    public $price;
    public $merchant_price;
    public $satoshiPrice;
    public $btcPrice;
    public $eurPrice;
    public $usdPrice;
    public $delivered;
    public $partialPayment;
    public $itemDesc;
    public $valuePackage;
    public $operatorSlug;
    public $invoiceTime;
    public $expirationTime;
    public $orderId;
    public $willRetry;
    public $allowRetry;
    public $paidAmount;

    /**
     * @var PinInfo
     */
    public $pinInfo;

    /**
     * @var Payment
     */
    public $payment;

    /**
     * @return Order
     */
    public function orderInfo()
    {
        return $this->getApiary()->orderInfo($this->orderId);
    }

    /**
     * @return Order
     */
    public function orderPurchase()
    {
        return $this->getApiary()->orderPurchase($this->orderId);
    }
}