<?php declare(strict_types=1);

namespace Yaspcc\Cache\Redis\Config;

use Symfony\Component\Yaml\Yaml;

/**
 * Class RedisConfig
 * @package Yaspcc\Config
 */
class RedisConfig
{
    /** @var string */
    private $host;
    /** @var int */
    private $port;
    /** @var string */
    private $scheme;
    /** @var string */
    const CONFIG_PATH = __DIR__ . "/config/redis.yaml";

    /**
     * RedisConfig constructor.
     * @param Yaml $yaml
     */
    public function __construct(Yaml $yaml)
    {
        $this->loadConfig($yaml);
    }

    /**
     * @param Yaml $yaml
     */
    private function loadConfig(Yaml $yaml): void
    {
        $array = $yaml->parseFile($this::CONFIG_PATH);
        $this->host = $array["host"];
        $this->port = $array["port"];
        $this->scheme = $array["scheme"];
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

}