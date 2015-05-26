<?php namespace Xjchen\Alauda;

class Util
{
    public static function toString($variable)
    {
        if (is_bool($variable)) {
            return $variable ? 'true' : 'false';
        } elseif (is_array($variable)) {
            return json_encode($variable);
        } else {
            return $variable;
        }
    }

    public static function getConfigFile()
    {
        $configFile = getenv("HOME") . DIRECTORY_SEPARATOR . '.alauda';
        return $configFile;
    }

    public static function getToken()
    {
        $configFile = static::getConfigFile();
        if (!file_exists($configFile)) {
            return null;
        }
        return json_decode(file_get_contents($configFile), true);
    }

    public static function saveToken($token, $username)
    {
        $configFile = static::getConfigFile();
        $data = [
            'token' => $token,
            'username' => $username,
        ];
        return file_put_contents($configFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    public static function clearToken()
    {
        $configFile = static::getConfigFile();
        if (file_exists($configFile)) {
            return unlink($configFile);
        }
        return true;
    }
}
