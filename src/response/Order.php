<?php

namespace bitrefill\response;

use bitrefill\base\Object;

class Order extends Object
{
    public $id;
    public $email;
    public $number;
    public $paymentReceived;
    public $accessToken;
    public $userRef;
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
     * @var \bitrefill\response\PinInfo
     */
    public $pinInfo;

    /**
     * @var \bitrefill\response\Payment
     */
    public $payment;

    /**
     * @return \bitrefill\response\Order
     */
    public function orderInfo()
    {
        return $this->getApiary()->orderInfo($this->orderId);
    }

    /**
     * @return \bitrefill\response\Order
     */
    public function orderPurchase()
    {
        return $this->getApiary()->orderPurchase($this->orderId);
    }

    /**
     * @param string $currency null
     * @return mixed
     */
    public function getPrice($currency = null)
    {
        if (!$currency) {
            $account = $this->getApiary()->accountBalance();
            $currency = $account->getCurrency();
        }

        switch ($currency) {
            case 'USD': return $this->usdPrice;
            case 'EUR': return $this->eurPrice;
            case 'BTC': return $this->btcPrice;
            default: return $this->satoshiPrice;
        }
    }
}