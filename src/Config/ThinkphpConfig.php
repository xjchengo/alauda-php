<?php namespace Xjchen\Alauda\Config;

class ThinkphpConfig extends AbstractConfig implements ConfigInterface
{
    private static $loaded = false;

    public function __construct($indexFilePath)
    {
        if (!self::$loaded) {
            self::loadFramework($indexFilePath);
            self::$loaded = true;
            // spl_autoload_unregister('Think\Think::autoload');
        }
    }

    public function getDbType(){
        return C('DB_TYPE');
    }

    public function getDbHost(){
        return C('DB_HOST');
    }

    public function getDbPort(){
        return C('DB_PORT') ?: 3306;
    }

    public function getDbName(){
        return C('DB_NAME');
    }

    public function getDbUser(){
        return C('DB_USER');
    }

    public function getDbPassword(){
        return C('DB_PWD');
    }

}
