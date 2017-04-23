<?php

namespace bitrefill\response;

use bitrefill\base\Object;

class Range extends Object
{
    public $min;
    public $max;
    public $step;
    public $customerSatoshiPriceRate;
    public $customerEurPriceRate;
    public $customerPriceRate;
    public $userPriceRate;
}