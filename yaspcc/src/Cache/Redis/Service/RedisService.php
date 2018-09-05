<?php declare(strict_types=1);

namespace Yaspcc\Cache\Redis\Service;

use Yaspcc\Cache\KeyValueCacheInterface;
use Yaspcc\Cache\Redis\RedisClientInterface;

/**
 * Class RedisService
 * @package Yaspcc\Cache\Redis\Service
 */
class RedisService implements KeyValueCacheInterface
{
    /**
     * @var RedisClientInterface
     */
    private $redisClient;

    /**
     * RedisService constructor.
     * @param RedisClientInterface $redisClient
     */
    public function __construct(RedisClientInterface $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    /**
     * @param string $key
     * @param string $value
     * @param null $expire
     */
    function set(string $key, string $value, $expire = null)
    {
        $this->redisClient->set($key,$value,$expire);
    }

    /**
     * @param string $key
     */
    function get(string $key): string
    {
        return $this->redisClient->get($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    function exists(string $key): bool
    {
        return $this->redisClient->exists($key);
    }
}