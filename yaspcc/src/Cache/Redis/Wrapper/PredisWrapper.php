<?php declare(strict_types=1);

namespace Yaspcc\Cache\Redis\Wrapper;

use Predis\Client;
use Yaspcc\Cache\Redis\RedisClientServiceInterface;
use Yaspcc\Cache\Redis\Config\RedisConfig;

class PredisWrapper implements RedisClientServiceInterface
{
    /** @var RedisConfig */
    private $redisConfig;
    /** @var Client */
    private $client;

    /**
     * PredisWrapper constructor.
     * @param RedisConfig $redisConfig
     */
    public function __construct(RedisConfig $redisConfig)
    {
        $this->redisConfig = $redisConfig;
        $this->client = $this->connectClient($redisConfig);
    }

    /**
     * @param RedisConfig $redisConfig
     * @return Client
     */
    private function connectClient(RedisConfig $redisConfig): Client
    {
        $client = new Client([
            'scheme' => $redisConfig->getScheme(),
            'host' => $redisConfig->getHost(),
            'port' => $redisConfig->getPort(),
            'password' => $redisConfig->getPassword()
        ]);
        $client->connect();
        return $client;
    }

    /**
     * @param string $key
     * @param string $value
     * @param null $expire
     */
    public function set(string $key, string $value, int $expire = null) : void
    {
        $this->client->set($key,$value);
        if(!empty($expire)) {
            $this->client->expire($key,$expire);
        }
    }

    /**
     * @param string $key
     * @return string
     */
    public function get(string $key): ?string
    {
        return $this->client->get($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return $this->client->exists($key) === 1;
    }

    /**
     * @param string[] $keys
     * @return string[]
     */
    public function getMany(array $keys): array
    {
        return $this->client->mget($keys);
    }
}