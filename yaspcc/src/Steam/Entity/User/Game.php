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
     * @var int
     */
    private $playtime;


    /**
     * Game constructor.
     * @param string $appid
     * @param string $playtime
     */
    public function __construct(
        int $appid,
        int $playtime
    ) {
        $this->appid = $appid;
        $this->playtime = $playtime;
    }

    /**
     * @return string
     */
    public function getPlaytime(): int
    {
        return $this->playtime;
    }

    /**
     * @return string
     */
    public function getAppid(): int
    {
        return $this->appid;
    }
}