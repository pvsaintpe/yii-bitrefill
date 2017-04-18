<?php

namespace bitrefill\response;


use bitrefill\base\Object;

class Package extends Object
{
    public $value;
    public $eurPrice;
    public $satoshiPrice;
    public $usdPrice;
}