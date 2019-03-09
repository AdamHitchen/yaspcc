<?php declare(strict_types=1);

namespace Yaspcc\Cron;

use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;
use Yaspcc\Cache\CacheServiceInterface;
use Yaspcc\Steam\Exception\ApiLimitExceededException;
use Yaspcc\Steam\Exception\GameNotFoundException;
use Yaspcc\Steam\Repository\GameRepository;

class Queue
{
    /**
     * @var GameRepository
     */
    private $gameRepository;
    /**
     * @var CacheServiceInterface
     */
    private $cache;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * QueueTest constructor.
     * @param GameRepository $gameRepository
     * @param CacheServiceInterface $cache
     * @param LoggerInterface $logger
     */
    public function __construct(
        GameRepository $gameRepository,
        CacheServiceInterface $cache,
        LoggerInterface $logger
    ) {
        $this->gameRepository = $gameRepository;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function processQueue()
    {
        if ($this->cache->exists("queue")) {
            $applist = json_decode($this->cache->get("queue"), true);
        } else {
            $queue = $this->gameRepository->getAllApps();
            $applist = $queue["applist"]["apps"];
        }
        $applistCount = count($applist);
        for ($i = 0; $i < min(150,$applistCount); $i++) {
            $app = array_pop($applist);
            //This call may be redundant - remove if so
            if ($this->cache->exists("game:" . $app["appid"])) {
                continue;
            }
            //get method also calls set for cache
            try {
                $this->gameRepository->get($app["appid"]);
            } catch (ApiLimitExceededException $exception) {
                $this->logger->alert("API Rate Limited");
            } catch (GameNotFoundException $exception) {
                $this->logger->alert("Error while getting game: " . $app["appid"]);
            }
        }

        $this->cache->set("queue", json_encode($applist));
    }
}