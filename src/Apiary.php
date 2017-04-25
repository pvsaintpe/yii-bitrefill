<?php

namespace bitrefill;

use bitrefill\response\Account;
use bitrefill\response\Country;
use bitrefill\response\LookupNumber;
use bitrefill\response\Order;
use CApplicationComponent;

/**
 * Class Apiary
 *
 * @author Veselov Pavel
 * @package bitrefill\components
 */
class Apiary extends CApplicationComponent
{
    protected $api_url = 'https://api.bitrefill.com/v1';
    protected $api_key;
    protected $api_secret;

    protected $initiated;

    /**
     * @throws Exception
     */
    protected function auth()
    {
        $this->initiated = false;
        $ch = curl_init($this->api_url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->api_key . ":" . $this->api_secret);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($ch);
        curl_close($ch);

        if (strpos($response, 'Hello World!') !== false) {
            $this->initiated = true;
        } else {
            throw new Exception('Your account does not have access to this resource');
        }
    }

    /**
     * @param string $url
     * @param array|null $params []
     * @param array|boolean $post false
     * @return mixed
     * @throws Exception
     */
    private function getResponse($url, $params = [], $post = false)
    {
        if (!$this->initiated) {
            $this->auth();
        }

        if (!empty($params)) {
            $url .= (($queryString = http_build_query($params)) ? '?' . $queryString : '');
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->api_key . ":" . $this->api_secret);
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
            throw new Exception(join(':', [
                $response['status'],
                $response['message'],
            ]));
        }

        if ($response && is_array($response) && isset($response['errorMessage'])) {
            throw new Exception(join(':', [
                $response['errorType'],
                $response['errorMessage'],
            ]));
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
    public function lookupNumber($number, $operatorSlug = null)
    {
        $response = $this->getResponse(
            $this->api_url . '/lookup_number',
            compact('number', 'operatorSlug')
        );

        $lookupNumber = new LookupNumber($response);
        $lookupNumber->setApiary($this);

        return $lookupNumber;
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
    public function orderPlace(
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
        $response = $this->getResponse(
            $this->api_url . '/order',
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

        $order = new Order($response);
        $order->setApiary($this);

        return $order;
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
    public function orderInfo($order_id)
    {
        $response = $this->getResponse(
            $this->api_url . '/order/' . $order_id,
            null,
            false
        );

        $order = new Order($response);
        $order->setApiary($this);

        return $order;
    }

    /**
     * Purchase an order you created
     *
     * @param $order_id
     * @return Order
     */
    public function orderPurchase($order_id)
    {
        $response = $this->getResponse(
            $this->api_url . "/order/$order_id/purchase",
            null,
            true
        );

        $order = new Order($response);
        $order->setApiary($this);

        return $order;
    }

    /**
     * Purchase a refill directly with your account balance.
     *
     * This call is a shortcut allowing you to create an order and pay for it at the same time.
     *
     * This route can only be used if you have a balance with us as this will pay the order
     * from your account instead of asking for a second payment call.
     *
     * @param string $operatorSlug
     * @param string $valuePackage
     * @param string $number
     * @param string $email
     *
     * @return Order
     */
    public function purchase($operatorSlug, $valuePackage, $number, $email)
    {
        $response = $this->getResponse(
            $this->api_url . '/purchase',
            null,
            compact('operatorSlug', 'valuePackage', 'number', 'email')
        );

        $order = new Order($response);
        $order->setApiary($this);

        return $order;
    }

    /**
     * Retrieve your account balance.
     *
     * @return Account
     */
    public function accountBalance()
    {
        $response = $this->getResponse(
            $this->api_url . '/account_balance'
        );

        $account = new Account($response);
        $account->setApiary($this);

        return $account;
    }

    /**
     * Get a list of all countries and telcos currently supported.
     * Update this list regularly as it changes.
     *
     * @return Country[]
     */
    public function getInventory()
    {
        $response = $this->getResponse(
            $this->api_url . '/inventory'
        );

        $countries = [];
        foreach ($response as $countryResponse) {
            $country = new Country($countryResponse);
            $country->setApiary($this);

            $countries[] = $country;
        }

        return $countries;
    }
}