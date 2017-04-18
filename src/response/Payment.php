<?php

namespace bitrefill\response;

use bitrefill\base\Object;

class Payment extends Object
{
    public $human;
    public $address;
    public $satoshiPrice;
    public $BIP21;
    public $BIP73;
}