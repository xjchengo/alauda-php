<?php namespace Xjchen\Alauda\Api;

class V1
{
    CONST ALAUDA_URL = 'https://api.alauda.cn/v1';

    public static $lastRawResponse;
    public static $lastCurlErrorCode;
    public static $lastCurlErrorMessage;

    public static function getAuthProfile($token)
    {
        $url = self::ALAUDA_URL . '/auth/profile';
        return static::requestWithToken($token, $url);
    }

    public static function getServicesWithServicePort($namespace, $token)
    {
        $list = self::getServices($namespace, $token);
        $results = [];
        foreach ($list['results'] as $service) {
            $results[] = self::getService($namespace, $service['service_name'], $token);
        }
        return $results;
    }

    public static function getServices($namespace, $token)
    {
        $url = self::ALAUDA_URL . '/services/' . $namespace;
        return static::requestWithToken($token, $url);
    }

    public static function getService($namespace, $serviceName, $token)
    {
        $url = self::ALAUDA_URL . '/services/' . $namespace . '/' . $serviceName;
        return static::requestWithToken($token, $url);
    }

    public static function requestWithToken($token, $url, $method = 'GET', $payload = [])
    {
        return static::request($url, $method, $payload, ['Authorization' => 'Token '.$token]);
    }

    public static function request($url, $method = 'GET', $payload = [], $headers = null)
    {
        $options = array(
            CURLOPT_URL            => $url,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
        );
        if ($method !== 'GET') {
            $options[CURLOPT_POSTFIELDS] = json_encode($payload);
        }
        if ($method === 'DELETE' || $method === 'PUT') {
            $options[CURLOPT_CUSTOMREQUEST] = $method;
        }
        if (count($headers) > 0) {
            $options[CURLOPT_HTTPHEADER] = self::compileRequestHeaders($headers);
        }
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $rawResponse = curl_exec($ch);
        self::$lastRawResponse = $rawResponse;
        $curlErrorCode = curl_errno($ch);
        self::$lastCurlErrorCode = $curlErrorCode;
        $curlErrorMessage = curl_error($ch);
        self::$lastCurlErrorMessage = $curlErrorMessage;
        if ($curlErrorCode) {
            throw new Exception($curlErrorMessage, $curlErrorCode);
        }
        return json_decode($rawResponse, true);
    }

    public static function compileRequestHeaders($headers)
    {
        $return = [];
        foreach ($headers as $key => $value) {
            $return[] = $key . ':' . $value;
        }
        return $return;
    }
}
