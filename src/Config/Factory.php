<?php namespace Xjchen\Alauda\Config;

class Factory
{
    public static $supportedFramework = ['laravel', 'thinkphp'];
    CONST MYSQL_CONTAINER = 'mysql-xjc';

    public static function guessFramework()
    {
        if (self::isThinkphp()) {
            return 'thinkphp';
        }
        return 'others';
    }

    public static function isThinkphp()
    {
        if (!file_exists('./index.php')) {
            return false;
        }
        $content = file_get_contents('./index.php');
        if (strpos($content, 'ThinkPHP.php') !== false) {
            return true;
        } else {
            return false;
        }
    }

    public static function getConfigRepository($framework)
    {
        $framework = strtolower($framework);
        if ($framework == 'thinkphp') {
            return new ThinkphpConfig('./index.php');
        } else {
            return new OtherConfig();
        }
    }
}
