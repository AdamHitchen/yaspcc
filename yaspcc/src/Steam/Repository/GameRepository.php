<?php declare(strict_types=1);

namespace Yaspcc\Steam\Repository;

use Psr\Log\LoggerInterface;
use Yaspcc\Cache\CacheServiceInterface;
use Yaspcc\Ratings\Service\RatingServiceInterface;
use Yaspcc\Steam\Entity\Game;
use Yaspcc\Steam\Exception\ApiLimitExceededException;
use Yaspcc\Steam\Exception\GameNotFoundException;
use Yaspcc\Steam\Request\GameRequest;

class GameRepository
{
    /**
     * @var CacheServiceInterface
     */
    private $cache;
    /**
     * @var GameRequest
     */
    private $gameRequest;
    /** @var array */
    private $ignoredGames;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var RatingServiceInterface
     */
    private $ratingService;

    /**
     * GameRepository constructor.
     * @param CacheServiceInterface $cache
     * @param GameRequest $gameRequest
     * @param RatingServiceInterface $ratingService
     * @param LoggerInterface $logger
     */
    public function __construct(
        CacheServiceInterface $cache,
        GameRequest $gameRequest,
        RatingServiceInterface $ratingService,
        LoggerInterface $logger
    ) {
        $this->cache = $cache;
        $this->gameRequest = $gameRequest;
        $this->logger = $logger;
        $this->ratingService = $ratingService;
    }

    /**
     * @param int $id
     * @return Game
     * @throws GameNotFoundException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Yaspcc\Steam\Exception\NoGameDataException
     */
    public function get(int $id): ?Game
    {
        if ($this->gameIsIgnored($id)) {
            return null;
        }

        if ($this->cache->exists("game:" . $id)) {
            $json = $this->cache->get("game:" . $id);
        } else {
            try {
                $game = $this->gameRequest->getGameByStoreApi($id);
                if (!$game->isLinuxNative) {
                    $game = $this->setGameAverageRating($game);
                }
            } catch (ApiLimitExceededException $exception) {
                $game = $this->gameRequest->getGame($id);
            } catch (GameNotFoundException $exception) {
                $this->addIgnoredId($id);
                throw new GameNotFoundException("This is probably not a game.");
            }
        }

        if (empty($json)) {
            if (empty($game)) {
                $this->addIgnoredId($id);
            } elseif (!$game->isComplete()) {
                $this->addToQueue($id);
            } elseif ($game->isComplete()) {
                $this->set($game);
                return $game;
            }

            throw new GameNotFoundException("Game not available");
        }

        $gameObj = $this->createGameFromJson($json);
        if (!$gameObj->isLinuxNative && (!$gameObj->averageRating || $this->hasNewAverage($gameObj))) {
            $this->setGameAverageRating($gameObj);
        }

        return $gameObj;
    }

    private function hasNewAverage(Game $game): bool
    {
        return $this->cache->exists('game:' . $game->id . ':average');
    }

    private function setGameAverageRating(Game $game): Game
    {
        $ratings = $this->ratingService->getGameRatings($game->id);

        if (is_null($ratings) || count($ratings) == 0) {
            return $game;
        }

        $game->averageRating = $this->ratingService->calculateAverageRating($ratings);
        $this->set($game);
        $this->cache->delete(["game:" . $game->id . ":average"]);
        return $game;
    }

    /**
     * @param string $json
     * @return Game
     */
    private function createGameFromJson(string $json): Game
    {
        $gameObj = json_decode($json);
        $game = new Game($gameObj->name, $gameObj->id);
        return $game->fromJson($gameObj);
    }

    private function setIgnoredGames(array $ignoredGames): void
    {
        $this->ignoredGames = $ignoredGames;
        $this->cache->set("game:ignore", json_encode($this->ignoredGames));
    }

    /**
     * @return array
     */
    public function getIgnoredGames(): array
    {
        if (!empty($this->ignoredGames)) {
            return $this->ignoredGames;
        }

        if ($this->cache->exists("game:ignore")) {
            $this->ignoredGames = json_decode($this->cache->get("game:ignore"), true);
            return $this->ignoredGames;
        }

        return [];
    }

    /**
     * @param int $gameId
     */
    private function addIgnoredId(int $gameId): void
    {
        $ignoreList = $this->getIgnoredGames();
        $ignoreList[] = $gameId;
        $this->setIgnoredGames($ignoreList);
    }

    private function gameIsIgnored(int $gameId): bool
    {
        return in_array($gameId, $this->getIgnoredGames());
    }

    /**
     * @param Game $game
     */
    public function set(Game $game): void
    {
        if (!$game->isComplete()) {
            $this->addToQueue($game->id);
        }

        $this->cache->set("game:" . $game->id, json_encode($game));
    }

    /**
     * @return array
     */
    public function getAllApps(): array
    {
        return $this->gameRequest->getAllApps();
    }

    /**
     * @param array $gameIds
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMany(array $gameIds): array
    {
        $keys = [];
        foreach ($gameIds as $gameId) {
            $keys[] = "game:" . $gameId;
        }
        $gamesJson = $this->cache->getMany($keys);
        $games = [];

        foreach ($gamesJson as $key => &$game) {
            if (is_null($game)) {
                try {
                    $game = $this->get((int)str_replace("game:", "", $keys[$key]));
                    if (is_null($game)) {
                        unset($gamesJson[$key]);
                        continue;
                    }
                } catch (\Throwable $e) {
                    $this->logger->error("Error while fetching game in GameRepository: " . $e->getMessage());
                }
            } else {
                $game = $this->createGameFromJson($game);
                if (!$game->isLinuxNative && (!$game->averageRating || $this->hasNewAverage($game))) {
                    $game = $this->setGameAverageRating($game);
                }
            }

            $games[$game->id] = $game;
        }
        return $games;
    }

    /**
     * @param $id
     */
    private function addToQueue(int $id): void
    {
        $this->cache->set("queue:game:" . $id, (string)$id);
    }
}