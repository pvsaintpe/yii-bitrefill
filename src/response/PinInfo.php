<?php

namespace bitrefill\response;

use bitrefill\base\Object;

class PinInfo extends Object
{
    public $instructions;
    public $pin;
    public $other;
}