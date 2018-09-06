<?php declare(strict_types=1);

namespace Yaspcc\Cron;

use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;
use Yaspcc\Cache\KeyValueCacheInterface;
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
     * @var KeyValueCacheInterface
     */
    private $cache;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Queue constructor.
     * @param GameRepository $gameRepository
     * @param KeyValueCacheInterface $cache
     * @param LoggerInterface $logger
     */
    public function __construct(
        GameRepository $gameRepository,
        KeyValueCacheInterface $cache,
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
        for ($i = 0; $i < 150; $i++) {
            $app = array_pop($applist);
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