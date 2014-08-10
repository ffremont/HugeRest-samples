<?php

$loader = require(__DIR__ . '/../../../vendor/autoload.php');

// LOGGER
$configurator = new \LoggerConfiguratorDefault();
\Logger::configure($configurator->parse(__DIR__.'/../../../src/main/resources/log4php.xml'));


\Huge\IoC\Container\SuperIoC::registerLoader(array($loader, 'loadClass'));

$ioc = new \Huge\Rest\WebAppIoC('huge-samples', '1.1', array(
    'maxBodySize' => 1024
));

$cache = new \Doctrine\Common\Cache\ArrayCache();

$ioc->setApiCacheImpl($cache);
$ioc->setCacheImpl($cache);
$ioc->addDefinitions(array(
    array(
        'class' => 'MyWebApi\Resources\Person',
        'factory' => \Huge\IoC\Factory\SimpleFactory::getInstance()
    ),
    array(
        'class' => 'MyWebApi\Log4phpFactory',
        'factory' => \Huge\IoC\Factory\SimpleFactory::getInstance()
    ),
    array(
        'class' => 'Huge\Rest\Interceptors\PerfInterceptor',
        'factory' => \Huge\IoC\Factory\SimpleFactory::getInstance()
    )
));
$ioc->addFiltersMapping(array(
    'Huge\Rest\Interceptors\PerfInterceptor' => '.*'
));


$ioc->run();
?>