<?php declare(strict_types=1);

namespace Yaspcc\Cron;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Yaspcc\Cache\CacheServiceInterface;
use Yaspcc\Steam\Exception\ApiLimitExceededException;
use Yaspcc\Steam\Exception\GameNotFoundException;
use Yaspcc\Steam\Exception\NoGameDataException;
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

    public function processQueue(): void
    {
        if ($this->cache->exists("queue")) {
            $applist = json_decode($this->cache->get("queue"), true);
        } else {
            $queue = $this->gameRepository->getAllApps();
            $applist = $queue["applist"]["apps"];
        }
        $applistCount = count($applist);
        for ($i = 0; $i < min(150, $applistCount); $i++) {
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
                $applist[]= $app;
                break;
            } catch (GameNotFoundException $exception) {
                $this->logger->alert("Error while getting game: " . $app["appid"]);
            } catch (GuzzleException $e) {
                $this->logger->error("Encounted guzzle exception: " . $e->getMessage());
            } catch (NoGameDataException $e) {
                $this->logger->alert("Game has no data! " . $e->getMessage());
            }
        }

        $this->cache->set("queue", json_encode($applist));
    }
}