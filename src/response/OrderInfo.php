<?php

namespace bitrefill\response;

use bitrefill\base\Object;

class OrderInfo extends Object
{
    public $paymentReceived;
    public $delivered;
    public $value;
    public $number;
}