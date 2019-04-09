<?php declare(strict_types=1);

namespace Yaspcc\Cache\Redis\Service;

use Yaspcc\Cache\CacheServiceInterface;
use Yaspcc\Cache\Redis\RedisClientServiceInterface;

/**
 * Class RedisService
 * @package Yaspcc\Cache\Redis\Service
 */
class RedisService implements CacheServiceInterface
{
    /**
     * @var RedisClientServiceInterface
     */
    private $redisClient;

    /**
     * RedisService constructor.
     * @param RedisClientServiceInterface $redisClient
     */
    public function __construct(RedisClientServiceInterface $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    /**
     * @param string $key
     * @param string $value
     * @param null $expire
     */
    public function set(string $key, string $value, int $expire = null) : void
    {
        $this->redisClient->set($key,$value,$expire);
    }

    /**
     * @param string $key
     * @return string
     */
    public function get(string $key): string
    {
        return $this->redisClient->get($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return $this->redisClient->exists($key);
    }
}