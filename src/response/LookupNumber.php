<?php

namespace bitrefill\response;

use bitrefill\base\Object;

class LookupNumber extends Object
{
    /**
     * @var \bitrefill\response\Country
     */
    public $country;

    /**
     * @var \bitrefill\response\Operator
     */
    public $operator;

    /**
     * @var \bitrefill\response\Operator[]
     */
    public $altOperators;
}