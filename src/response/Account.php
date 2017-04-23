<?php

namespace bitrefill\response;

use bitrefill\base\Object;

class Account extends Object
{
    public $balance;
    public $currency;

    /**
     * @return string
     */
    public function getCurrency()
    {
        if ($this->currency == 'XBT') {
            return 'BTC';
        }

        return $this->currency;
    }

    /**
     * @return float|int
     */
    public function getBalance()
    {
        if ($this->currency == 'XBT') {
            return $this->balance / 100000000;
        }

        return $this->balance;
    }
}