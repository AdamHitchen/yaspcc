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
     * @param $id
     * @return Game
     */
    public function get(int $id): Game
    {
        if ($this->cache->exists("game:".$id)) {
            $json = $this->cache->get("game:".$id);
            $GLOBALS["cache"]++;
        } else {
            try {
                $game = $this->gameRequest->getGameByStoreApi($id);
                $GLOBALS["store"]++;
                $this->set($game);
            } catch (ApiLimitExceededException $exception) {
                $GLOBALS["dev"]++;
                $game = $this->gameRequest->getGame($id);
            }
        }

        if (empty($json) && (empty($game) || !$game->isComplete())) {
            $this->addToQueue($id);
        } else if(empty($json) && $game->isComplete()) {
            return $game;
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
        return  $game->fromJson($gameObj);
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
     * @param $id
     */
    private function addToQueue($id)
    {
        $this->cache->set("queue:game:" . $id, $id);
    }
}