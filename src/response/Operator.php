<?php

namespace bitrefill\response;

use bitrefill\base\Object;

class Operator extends Object
{
    public $name;
    public $slug;
    public $logoImage;
    public $currency;
    public $isRanged;
    public $isPinBased;

    /**
     * @var Range
     */
    public $range;

    /**
     * @var Package[]
     */
    public $packages;
}