<?php namespace Xjchen\Alauda\Config;

interface ConfigInterface
{

    public function getDbType();

    public function getDbHost();

    public function getDbPort();

    public function getDbName();

    public function getDbUser();

    public function getDbPassword();

}
