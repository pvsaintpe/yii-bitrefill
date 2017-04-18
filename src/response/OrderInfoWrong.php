<?php

namespace bitrefill\response;

use bitrefill\base\Object;

class OrderInfoWrong extends Object
{
    public $paymentRecieved;
    public $delivered;
    public $errorType;
    public $errorMessage;
}