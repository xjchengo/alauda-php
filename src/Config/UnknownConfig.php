<?php namespace Xjchen\Alauda\Config;

class UnknownConfig extends AbstractConfig implements ConfigInterface
{

    public function getDbType(){
        return 'mysql';
    }

    public function getDbHost(){
        return 'localhost';
    }

    public function getDbPort(){
        return 3306;
    }

    public function getDbName(){
        return 'alauda';
    }

    public function getDbUser(){
        return 'alauda';
    }

    public function getDbPassword(){
        return 'secret';
    }

}
