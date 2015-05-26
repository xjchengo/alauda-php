<?php namespace Xjchen\Alauda\Config;

use Exception;

abstract class AbstractConfig
{
    
    public static function loadFramework($indexFilePath)
    {
        try {
            ob_start();
            require $indexFilePath;
            ob_end_clean();
        } catch (Exception $e) {

        }
        
    }
}
