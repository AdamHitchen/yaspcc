<?php declare(strict_types=1);

require_once('vendor/autoload.php');
const ROOT_DIR = __DIR__ . '/';

/** @var \DI\Container $container */
$container = include ROOT_DIR . 'config/bootstrap.php';

/** @var \Yaspcc\Cron\Queue $request */
$request = $container->make('\Yaspcc\Cron\Queue');
$request->processQueue();