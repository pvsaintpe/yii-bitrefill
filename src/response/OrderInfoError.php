<?php

namespace bitrefill\response;

use bitrefill\base\Object;

class OrderInfoError extends Object
{
    public $message;
    public $status;
}