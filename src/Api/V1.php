<?php namespace Xjchen\Alauda\Api;

use Exception;

class V1
{
    CONST ALAUDA_URL = 'https://api.alauda.cn/v1';

    public static $lastRawResponse;
    public static $lastCurlErrorCode;
    public static $lastCurlErrorMessage;
    public static $lastResponseHttpStatusCode;

    public static function getAuthProfile($token)
    {
        $url = self::ALAUDA_URL . '/auth/profile';
        return static::requestWithToken($token, $url);
    }

    public static function generateToken($username, $password)
    {
        $url = self::ALAUDA_URL . '/generate-api-token';
        $payload = [
            'username' => $username,
            'password' => $password,
        ];
        return static::request($url, 'POST', $payload);
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

    public static function createService($namespace, $payload, $token)
    {
        $url = self::ALAUDA_URL . '/services/' . $namespace;
        $payload = json_encode($payload, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
        $headers = [
            'Content-Type' => 'application/json; charset=utf-8',
            'Content-Length' => strlen($payload),
            'Authorization' => 'Token '.$token
        ];
        return static::request($url, 'POST', $payload, $headers);
    }

    public static function destroyService($namespace, $serviceName, $token)
    {
        $url = self::ALAUDA_URL . '/services/' . $namespace . '/' . $serviceName;
        return static::requestWithToken($token, $url, 'DELETE');
    }

    public static function getServiceLogs($namespace, $serviceName, $token)
    {
        $endTime = time();
        $startTime = $endTime - 604800; // max 7 days.

        $url = self::ALAUDA_URL . '/services/' . $namespace . '/' . $serviceName . '/logs?start_time=' . $startTime . '&end_time=' . $endTime;
        return static::requestWithToken($token, $url);
    }

    public static function getInstances($namespace, $serviceName, $token)
    {
        $url = self::ALAUDA_URL . '/services/' . $namespace . '/' . $serviceName . '/instances';
        return static::requestWithToken($token, $url);
    }

    public static function getInstance($namespace, $serviceName, $uuid, $token)
    {
        $url = self::ALAUDA_URL . '/services/' . $namespace . '/' . $serviceName . '/instances/' . $uuid;
        return static::requestWithToken($token, $url);
    }

    public static function getRepositories($namespace, $token)
    {
        $url = self::ALAUDA_URL . '/repositories/' . $namespace;
        return static::requestWithToken($token, $url);
    }

    public static function getRepositoryTags($namespace, $repoName, $token)
    {
        $url = self::ALAUDA_URL . '/repositories/' . $namespace . '/' . $repoName . '/tags';
        return static::requestWithToken($token, $url);
    }

    public static function destroyRepository($namespace, $repoName, $token)
    {
        $url = self::ALAUDA_URL . '/repositories/' . $namespace . '/' . $repoName;
        return static::requestWithToken($token, $url, 'DELETE');
    }

    public static function createRepository($namespace, $repoName, $isPublic, $description, $token)
    {
        $url = self::ALAUDA_URL . '/repositories/' . $namespace;
        $payload = [
            'repo_name' => $repoName,
            'description' => $description,
            'namespace' => $namespace,
            'is_public' => $isPublic
        ];
        $payload = json_encode($payload, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
        $headers = [
            'Content-Type' => 'application/json; charset=utf-8',
            'Content-Length' => strlen($payload),
            'Authorization' => 'Token '.$token
        ];
        return static::request($url, 'POST', $payload, $headers);
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
            // $options[CURLOPT_POSTFIELDS] = http_build_query($payload, null, '&');
            $options[CURLOPT_POSTFIELDS] = $payload;
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
        $responseHttpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        self::$lastResponseHttpStatusCode = $responseHttpStatusCode;
        if ($curlErrorCode) {
            throw new Exception($curlErrorMessage, $curlErrorCode);
        }
        if ($responseHttpStatusCode >= 300) {
            throw new Exception($rawResponse, $responseHttpStatusCode);
        }
        return json_decode($rawResponse, true);
    }

    public static function compileRequestHeaders($headers)
    {
        $return = [];
        foreach ($headers as $key => $value) {
            $return[] = $key . ': ' . $value;
        }
        return $return;
    }
}
