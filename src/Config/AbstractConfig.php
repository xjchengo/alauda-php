<?php namespace Xjchen\Alauda\Config;

abstract class AbstractConfig
{
    
    public static function loadFramework($indexFilePath)
    {
        ob_start();
        require $indexFilePath;
        ob_end_clean();
    }
}
