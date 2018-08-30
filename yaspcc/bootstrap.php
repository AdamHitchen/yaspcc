<?php declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$monolog = new \Monolog\Logger("Log");
$monolog->pushHandler(new StreamHandler('debug.log',Logger::DEBUG));

try {
    $containerBuilder = new ContainerBuilder();
    $containerBuilder->useAutowiring(true);
    $container = $containerBuilder->build();

    $container->set(\Psr\Log\LoggerInterface::class,$monolog);

    return $container;

} catch (\Exception $e) {
    $monolog->log($monolog::CRITICAL, "ERROR INITIALIZING APPLICATION " . $e->getMessage());
    throw $e;
}