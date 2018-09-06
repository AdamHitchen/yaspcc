<?php declare(strict_types=1);

namespace Yaspcc\Steam\Repository;

use Yaspcc\Cache\KeyValueCacheInterface;
use Yaspcc\Steam\Entity\Game;
use Yaspcc\Steam\Exception\ApiLimitExceededException;
use Yaspcc\Steam\Exception\GameNotFoundException;
use Yaspcc\Steam\Request\GameRequest;

class GameRepository
{
    /**
     * @var KeyValueCacheInterface
     */
    private $cache;
    /**
     * @var GameRequest
     */
    private $gameRequest;

    /**
     * GameRepository constructor.
     * @param KeyValueCacheInterface $cache
     * @param GameRequest $gameRequest
     */
    public function __construct(KeyValueCacheInterface $cache, GameRequest $gameRequest)
    {
        $this->cache = $cache;
        $this->gameRequest = $gameRequest;
    }

    /**
     * @param int $id
     * @return Game
     * @throws GameNotFoundException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Yaspcc\Steam\Exception\NoGameDataException
     */
    public function get(int $id): Game
    {
        if ($this->cache->exists("game:" . $id)) {
            $json = $this->cache->get("game:" . $id);
            $GLOBALS["cache"]++;
        } else {
            try {
                $game = $this->gameRequest->getGameByStoreApi($id);
                $GLOBALS["store"]++;
                $this->set($game);
            } catch (ApiLimitExceededException $exception) {
                $GLOBALS["dev"]++;
                $game = $this->gameRequest->getGame($id);
            } catch (GameNotFoundException $exception) {
                $this->addIgnoredId($id);
                throw new GameNotFoundException("This is probably not a game.");
            }
        }

        if(empty($json)){
            if(empty($game)) {
                $this->addIgnoredId($id);
            } else if (!$game->isComplete()) {
                $this->addToQueue($id);
            } else if ($game->isComplete()) {
                return $game;
            }

            throw new GameNotFoundException("Game not available");
        }

        return $this->createGameFromJson($json);
    }

    /**
     * @param string $json
     * @return Game
     */
    private function createGameFromJson(string $json): Game
    {
        //TODO: implement createGameFromJson()
        $gameObj = json_decode($json);
        $game = new Game($gameObj->name, $gameObj->id);
        return $game->fromJson($gameObj);
    }

    /**
     * @return array
     */
    public function getIgnoreList(): array
    {
        if($this->cache->exists("game:ignore")){
            return json_decode($this->cache->get("game:ignore"),true);
        }

        return [];
    }

    /**
     * @param int $gameId
     */
    private function addIgnoredId(int $gameId)
    {
        if ($this->cache->exists("game:ignore")) {
            $arr = json_decode($this->cache->get("game:ignore"), true);
            $arr[] = $gameId;
            $this->cache->set("game:ignore", json_encode($arr));
        } else {
            $this->cache->set("game:ignore", json_encode([$gameId]));
        }
    }

    /**
     * @param $id
     */
    public function set(Game $game)
    {
        if (!$game->isComplete()) {
            $this->addToQueue($game);
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
     * @param $id
     */
    private function addToQueue($id)
    {
        $this->cache->set("queue:game:" . $id, $id);
    }
}