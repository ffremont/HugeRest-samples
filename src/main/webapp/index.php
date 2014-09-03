<?php
$start = microtime(true);
$loader = require(__DIR__ . '/../../../vendor/autoload.php');

$resource = __DIR__.'/../resources';
$configs = require($resource.'/config.php');

\Huge\IoC\Container\SuperIoC::registerLoader(array($loader, 'loadClass'));

$ioc = new \Huge\Rest\WebAppIoC('huge-samples', '1.1', array(
    'maxBodySize' => 1024
));

//$cache = new \Doctrine\Common\Cache\ArrayCache();
$memcache = new Memcache();
    $memcache->connect('127.0.0.1', 11211);
    $cache = new \Doctrine\Common\Cache\MemcacheCache();
    $cache->setMemcache($memcache);

$ioc->setApiCacheImpl($cache);
$ioc->setCacheImpl($cache);


$kloggerFactory = new \Huge\IoC\Factory\ConstructFactory(array($configs['klogger']['path'], $configs['klogger']['level']));
$ioc->addDefinitions(array(
    array(
        'class' => 'MyWebApi\Resources\Person',
        'factory' => \Huge\IoC\Factory\SimpleFactory::getInstance()
    ),
    array(
        'class' => 'MyWebApi\KLoggerFactory',
        'factory' => $kloggerFactory
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