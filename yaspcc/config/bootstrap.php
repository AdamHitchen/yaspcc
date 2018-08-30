<?php declare(strict_types=1);

/**
 * Setup DI container
 */

use DI\ContainerBuilder;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$monolog = new \Monolog\Logger("Log");
$monolog->pushHandler(new StreamHandler('debug.log', Logger::DEBUG));

try {

    $containerBuilder = new ContainerBuilder();
    $containerBuilder->useAutowiring(true);
    $container = $containerBuilder->build();

    $container->set(\Psr\Log\LoggerInterface::class, $monolog);
    $container->set(\Yaspcc\Routing\RouterInterface::class, $container->make(\Yaspcc\Routing\SymfonyRouter::class));

    return $container;

} catch (\Exception $exception) {
    $monolog->log($monolog::CRITICAL, "ERROR INITIALIZING APPLICATION " . $e->getMessage());
    throw $exception;
}