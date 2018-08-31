<?php declare(strict_types=1);

namespace Yaspcc\Steam\Config;

use Symfony\Component\Yaml\Yaml;

/**
 * Class Config
 * @package Yaspcc\Steam\Config
 */
class Config
{

    const CONFIG_PATH = ROOT_DIR . "config/steam.yaml";

    /** @var string */
    private $apiKey;
    /** @var string */
    private $baseUrl;

    /**
     * Config constructor.
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
        $array = $yaml->parseFile(self::CONFIG_PATH);
        $this->apiKey = $array["key"];
        $this->baseUrl = $array["url"];
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }


}