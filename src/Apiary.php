<?php

namespace bitrefill;

use bitrefill\response\Account;
use bitrefill\response\Country;
use bitrefill\response\LookupNumber;
use bitrefill\response\Order;

/**
 * Class Apiary
 *
 * @author Veselov Pavel
 * @package bitrefill\components
 */
abstract class Apiary
{
    CONST API_URL = 'https://api.bitrefill.com/v1';

    private static $_initiated;

    /**
     * @return string
     */
    abstract protected static function getApiKey();

    /**
     * @return string
     */
    abstract protected static function getApiSecret();

    /**
     * @param $message
     * @param $type
     * @throws
     */
    private static function error($message, $type = 'error')
    {
        throw new Exception(join(':', [$type, $message]));
    }

    /**
     * @throws Exception
     */
    private static function _auth()
    {
        $ch = curl_init(static::API_URL);
        curl_setopt($ch, CURLOPT_USERPWD, static::getApiKey() . ":" . static::getApiSecret());
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response == 'Hello World!') {
            static::$_initiated = true;
        } else {
            static::error('Your account does not have access to this resource', 'Unhautorized');
        }
    }

    /**
     * @param string $url
     * @param array|null $params []
     * @param array|boolean $post false
     * @return mixed
     * @throws Exception
     */
    private static function getResponse($url, $params = [], $post = false)
    {
        if (!static::$_initiated) {
            static::_auth();
        }

        if (!empty($params)) {
            $url .= (($queryString = http_build_query($params)) ? '?' . $queryString : '');
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json"
        ));

        if ($post === false) {
            curl_setopt($ch, CURLOPT_POST, false);
        } else {
            curl_setopt($ch, CURLOPT_POST, true);

            if (is_array($post) && !empty($post)) {
                $post = array_filter($post, function ($value) {
                    return ($value === null) ? false : true;
                });
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
            }
        }

        $response = curl_exec($ch);
        $response = json_decode($response, true);
        curl_close($ch);

        if ($response && is_array($response) && isset($response['message'])) {
            static::error($response['errorMessage'], $response['errorType']);
        }

        return $response;
    }

    /**
     *
     * Check if this number is supported and try to find it's operator.
     *
     * If the number is supported and the operator is found, you will get back a full pricelist.
     *
     * If we could not find the operator, but the country is supported, you will get a list of potential operators,
     * you can then retry the call by specifying an operator to get the full pricelist.
     *
     * If you check for a number with a specific operator, and it's not supported - you will get an error.
     *
     * If the country is not supported you will get a "This country is not supported" error.
     *
     * @param string $number
     * @param string $operatorSlug null
     *
     * @throws
     *
     * @return LookupNumber
     */
    public static function lookupNumber($number, $operatorSlug = null)
    {
        $response = static::getResponse(
            static::API_URL . '/lookupNumber',
            compact('number', 'operatorSlug')
        );

        return new LookupNumber($response);
    }

    /**
     * Place an order for a particular number to be refilled.
     *
     * Returns bitcoin payment instructions and USD/EUR prices, refer to the Overview section for
     * more informations on how to pay for that order.
     *
     * @param $operatorSlug
     * @param $valuePackage
     * @param $number
     * @param $email
     * @param bool $sendEmail
     * @param bool $sendSMS
     * @param null $refund_btc_address
     * @param null $webhook_url
     * @param null $userRef
     *
     * @return Order
     */
    public static function orderPlace(
        $operatorSlug,
        $valuePackage,
        $number,
        $email,
        $sendEmail = true,
        $sendSMS = true,
        $refund_btc_address = null,
        $webhook_url = null,
        $userRef = null
    )
    {
        $response = static::getResponse(
            static::API_URL . '/order',
            null,
            compact(
                'operatorSlug',
                'valuePackage',
                'number',
                'email',
                'sendEmail',
                'sendSMS',
                'refund_btc_address',
                'webhook_url',
                'userRef'
            )
        );

        return new Order($response);
    }

    /**
     * Get order info for an order.
     *
     * Can be used to poll for the status of an order you sent.
     *
     * If order is unpaid, it will contain payment info.
     *
     * If order is paid - it will contain delivery info.
     *
     * @param $order_id
     * @return Order
     */
    public static function orderInfo($order_id)
    {
        $response = static::getResponse(
            static::API_URL . '/order',
            compact('order_id'),
            false
        );

        return new Order($response);
    }

    /**
     * Purchase an order you created
     *
     * @param $order_id
     * @return Order
     */
    public static function orderPurchase($order_id)
    {
        $response = static::getResponse(
            static::API_URL . "/order/$order_id/purchase",
            null,
            true
        );

        return new Order($response);
    }

    /**
     * Purchase a refill directly with your account balance.
     *
     * This call is a shortcut allowing you to create an order and pay for it at the same time.
     *
     * This route can only be used if you have a balance with us as this will pay the order
     * from your account instead of asking for a second payment call.
     *
     * @param $operator
     * @param $valuePackage
     * @param $number
     * @param $email
     *
     * @return Order
     */
    public static function purchase($operator, $valuePackage, $number, $email)
    {
        $response = static::getResponse(
            static::API_URL . '/purchase',
            null,
            compact('operator', 'valuePackage', 'number', 'email')
        );

        return new Order($response);
    }

    /**
     * Retrieve your account balance.
     *
     * @return Account
     */
    public static function accountBalance()
    {
        $response = static::getResponse(
            static::API_URL . '/account'
        );

        return new Account($response);
    }

    /**
     * Get a list of all countries and telcos currently supported.
     * Update this list regularly as it changes.
     *
     * @return Country[]
     */
    public static function getInventory()
    {
        $response = static::getResponse(
            static::API_URL . '/inventory'
        );

        $countries = [];
        foreach ($response as $country) {
            $countries[] = new Country($country);
        }

        return $countries;
    }
}