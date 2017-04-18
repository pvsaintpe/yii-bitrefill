<?php

namespace bitrefill\response;

class Operator extends AltOperator
{
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