<?php declare(strict_types=1);

namespace Yaspcc\Steam\Entity\User;

/**
 * Class Game
 * @package Yaspcc\Steam\Entity\User
 */
class Game
{
    /**
     * @var int
     */
    public $appid;
    /**
     * @var int|null
     */
    private $playtime;


    /**
     * Game constructor.
     * @param int $appid
     * @param int|null $playtime
     */
    public function __construct(
        int $appid,
        ?int $playtime
    ) {
        $this->appid = $appid;
        $this->playtime = $playtime;
    }

    /**
     * @return int
     */
    public function getPlaytime(): ?int
    {
        return $this->playtime;
    }

    /**
     * @return int
     */
    public function getAppid(): int
    {
        return $this->appid;
    }
}