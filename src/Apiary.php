<?php

namespace bitrefill;

use bitrefill\response\OrderInfoError;
use bitrefill\response\LookupNumber;
use bitrefill\response\Order;
use bitrefill\response\OrderInfo;
use bitrefill\response\OrderInfoWithPin;
use bitrefill\response\OrderInfoWrong;

/**
 * Class Apiary
 *
 * @author Veselov Pavel
 * @package bitrefill\components
 */
class Apiary
{
    protected static $api_key;
    protected static $api_secret;

    const API_URL = 'https://api.bitrefill.com/v1';

    /**
     * @param string $url
     * @param array|null $params []
     * @param array|boolean $post false
     * @return mixed
     * @throws Exception
     */
    private static function getResponse($url, $params = [], $post = false)
    {
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

        if (is_array($post) && !empty($post)) {
            $post = array_filter($post, function ($value) {
                return ($value === null) ? false : true;
            });
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        } else {
            curl_setopt($ch, CURLOPT_POST, false);
        }

        $response = curl_exec($ch);
        $response = json_decode($response, true);
        curl_close($ch);

        if ($response && is_array($response) && isset($response['error_code'])) {
            throw new Exception($response['message'], $response['error_code']);
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
    public static function order(
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
     * @return OrderInfo|OrderInfoWithPin|OrderInfoWrong|OrderInfoError
     */
    public static function orderInfo($order_id)
    {
        $response = static::getResponse(
            static::API_URL . '/order',
            compact('order_id')
        );

        switch (true) {
            case (isset($response['pinInfo'])):
                return new OrderInfoWithPin($response);
            case (isset($response['errorType'])):
                return new OrderInfoWrong($response);
            case (isset($response['message'])):
                return new OrderInfoError($response);
            default:
                return new OrderInfo($response);
        }
    }


    
}