<?php declare(strict_types=1);

require_once('vendor/autoload.php');

/** @var \DI\Container $container */
$container = include __DIR__ . '/config/bootstrap.php';

/** @var \Yaspcc\Cron\UpdateProtonDBRatings $dbratings */
$dbratings = $container->make(\Yaspcc\Cron\UpdateProtonDBRatings::class);
$dbratings->run(__DIR__);
/** @var \Yaspcc\Cron\Queue $request */
$request = $container->make('\Yaspcc\Cron\Queue');
$request->processQueue();