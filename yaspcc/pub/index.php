<?php declare(strict_types=1);

use Yaspcc\Config\RedisConfig;

require_once('../vendor/autoload.php');
const ROOT_DIR = __DIR__ . '/../';

const ROOT_DIR = __DIR__ . '/../';

/** @var \DI\Container $container */
$container = include __DIR__ . '/../config/bootstrap.php';

// Match routes
/** @var \Yaspcc\Routing\RouterInterface $router */
$router = $container->get(Yaspcc\Routing\RouterInterface::class);
$router->match();

