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
    $container->set(\Yaspcc\Api\Routing\RouterInterface::class, $container->make(\Yaspcc\Api\Routing\SymfonyRouter::class));
    $container->set(\Yaspcc\Cache\Redis\RedisClientServiceInterface::class, $container->make(\Yaspcc\Cache\Redis\Wrapper\PredisWrapper::class));
    $container->set(\Yaspcc\Cache\CacheServiceInterface::class, $container->make(\Yaspcc\Cache\Redis\Service\RedisService::class));
    $container->set(\Yaspcc\Ratings\Service\RatingServiceInterface::class, $container->make(\Yaspcc\Ratings\Service\ProtonDBService::class));

    return $container;

} catch (\Exception $exception) {
    $monolog->log($monolog::CRITICAL, "ERROR INITIALIZING APPLICATION " . $exception->getMessage());
    throw $exception;
}