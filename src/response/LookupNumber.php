<?php

namespace bitrefill\response;

use bitrefill\base\Object;

/*
{
  "country": {
    "alpha2": "UA",
    "name": "Ukraine",
    "slug": "ukraine",
    "countryCallingCodes": [
      "+380"
    ],
    "currencies": [
      "UAH"
    ]
  },
  "operator": {
    "name": "MTS Ukraine",
    "slug": "mts-ukraine",
    "currency": "UAH",
    "logoImage": "https://dafitb341j2qo.cloudfront.net/d_operator.png/mts-ukraine.png",
    "isRanged": false,
    "isPinBased": false,
    "range": {
      "min": 10,
      "max": 2000,
      "step": 1,
      "customerSatoshiPriceRate": 5365,
      "customerEurPriceRate": "0.038064"
    },
    "packages": [
      {
        "value": "200",
        "eurPrice": 7.61,
        "satoshiPrice": 1080000,
        "usdPrice": 8.18
      },
      {
        "value": "400",
        "eurPrice": 15.23,
        "satoshiPrice": 2150000,
        "usdPrice": 16.37
      },
      {
        "value": "600",
        "eurPrice": 22.84,
        "satoshiPrice": 3220000,
        "usdPrice": 24.55
      },
      {
        "value": "800",
        "eurPrice": 30.45,
        "satoshiPrice": 4300000,
        "usdPrice": 32.73
      },
      {
        "value": "1000",
        "eurPrice": 38.06,
        "satoshiPrice": 5370000,
        "usdPrice": 40.92
      },
      {
        "value": "1200",
        "eurPrice": 45.68,
        "satoshiPrice": 6440000,
        "usdPrice": 49.1
      }
    ]
  },
  "altOperators": [
    {
      "name": "Kyivstar Ukraine",
      "slug": "kyivstar-ukraine",
      "logoImage": "https://dafitb341j2qo.cloudfront.net/d_operator.png/kyivstar-ukraine.png"
    },
    {
      "name": "Beeline Ukraine",
      "slug": "beeline-ukraine",
      "logoImage": "https://dafitb341j2qo.cloudfront.net/d_operator.png/beeline-ukraine.png"
    },
    {
      "name": "Utel Ukraine",
      "slug": "utel-ukraine",
      "logoImage": "https://dafitb341j2qo.cloudfront.net/d_operator.png/utel-ukraine.png"
    }
  ]
}
*/
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
     * @var AltOperator[]
     */
    public $altOperators;
}