<?php

namespace bitrefill\response;

use bitrefill\base\Object;

class Country extends Object
{
    public $alpha2;
    public $name;
    public $slug;
    public $countryCallingCodes;
    public $countryCode;
    public $currencies;

    /**
     * @var \bitrefill\response\Operator[]
     */
    public $operators;
}