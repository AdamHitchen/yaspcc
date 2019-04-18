<?php declare(strict_types=1);

namespace Yaspcc\Steam\Entity\User;

class Profile
{
    /** @var int */
    public $userId;
    /** @var array */
    public $games;
    /** @var */
    public $date;
    /** @var string|null */
    public $username;

    /**
     * Profile constructor.
     * @param $userId
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
        $this->games = [];
    }

    /**
     * @param \stdClass $response
     * @return Profile
     */
    public function fromRequestJson(\stdClass $response): Profile
    {
        foreach ($response->response->games as $game) {
            $this->games[$game->appid] = new Game($game->appid, $game->playtime_forever);
        }

        return $this;
    }

    /**
     * @param \stdClass $response
     * @return Profile
     */
    public function fromJson(\stdClass $response): Profile
    {
        foreach ($response->games as $game) {
            $this->games[$game->appid] = new Game($game->appid, $game->playtime_forever ?? 0);
        }

        return $this;
    }

}