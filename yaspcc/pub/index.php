<?php declare(strict_types=1);

require_once('../vendor/autoload.php');

/** @var \DI\Container $container */
$container = include __DIR__ . '/../bootstrap.php';

// Match routes
/** @var \Yaspcc\Routing\RouterInterface $router */
$router = $container->get(Yaspcc\Routing\RouterInterface::class);
$router->match();

