<?php

namespace bitrefill;

/**
 * Class Apiary
 *
 * @author Veselov Pavel
 * @package bitrefill\components
 */
class Apiary
{
    /**
     * @param string $url
     * @param array $params []
     * @param array|boolean $post false
     * @return mixed
     * @throws Exception
     */
    private static function getResponse($url, $params = [], $post = false)
    {
        $url .= (($queryString = http_build_query($params)) ? '?' . $queryString : '');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        if (is_array($post) && !empty($post)) {
            $post = array_filter($post, function ($value) {
                return ($value === null) ? false : true;
            });
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        } else {
            curl_setopt($ch, CURLOPT_POST, false);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $response = json_decode($response, true);

        if ($response && is_array($response) && isset($response['error_code'])) {
            throw new Exception($response['message'], $response['error_code']);
        }

        return $response;
    }


}