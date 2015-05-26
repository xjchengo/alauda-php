#!/usr/bin/env php
<?php
// application.php

foreach ([__DIR__ . '/../../autoload.php', __DIR__ . '/../vendor/autoload.php', __DIR__ . '/vendor/autoload.php'] as $file) {
    if (file_exists($file)) {
        define('ALAUDA_COMPOSER_INSTALL', $file);
        break;
    }
}
require ALAUDA_COMPOSER_INSTALL;

use Xjchen\Alauda\Config\Factory as ConfigFactory;
use Xjchen\Alauda\Api\V1 as ApiV1;
// $config = new Xjchen\Alauda\Config\ThinkphpConfig('/home/vagrant/Projects/thinkphp/index.php');
// var_dump($config->getDbType());
// var_dump($config->getDbHost());
// var_dump($config->getDbPort());
// var_dump($config->getDbName());
// var_dump($config->getDbUser());
// var_dump($config->getDbPassword());

// echo Xjchen\Alauda\Config\Factory::guessFramework();

$framework = ConfigFactory::guessFramework();
$config = ConfigFactory::getConfigRepository($framework);
var_dump($config->getDbType());
// var_dump($config->getDbHost());
// var_dump($config->getDbPort());
// var_dump($config->getDbName());
// var_dump($config->getDbUser());
// var_dump($config->getDbPassword());

// var_dump(ApiV1::getAuthProfile('231d856c83ce1f05448896cc132dea4f12c8ac5c'));
