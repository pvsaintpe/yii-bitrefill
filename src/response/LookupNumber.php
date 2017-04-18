<?php

namespace bitrefill\response;

use bitrefill\base\Object;

class LookupNumber extends Object
{
    /**
     * @var Country
     */
    public $country;

    /**
     * @var Operator
     */
    public $operator;

    /**
     * @var Operator[]
     */
    public $altOperators;
}