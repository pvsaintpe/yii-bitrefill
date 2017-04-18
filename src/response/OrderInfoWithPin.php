<?php

namespace bitrefill\response;

class OrderInfoWithPin extends OrderInfo
{
    /**
     * @var PinInfo
     */
    public $pinInfo;
}