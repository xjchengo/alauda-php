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
}
