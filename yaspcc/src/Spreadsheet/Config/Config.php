<?php declare(strict_types=1);

namespace Yaspcc\Spreadsheet\Config;

use Symfony\Component\Yaml\Yaml;

/**
 * Class Config
 * @package Yaspcc\Steam\Config
 */
class Config
{
    const CONFIG_PATH = ROOT_DIR . "config/google.yaml";

    /** @var string */
    private $spreadsheetId;
    /** @var string */
    private $cellRange;
    /** @var string */
    private $authConfigPath = ROOT_DIR . "config/googlecreds.json";

    /**
     * Config constructor.
     * @param Yaml $yaml
     */
    public function __construct(Yaml $yaml)
    {
        $this->loadConfig($yaml);
    }

    /**
     * @return string
     */
    public function getAuthPath()
    {
        return $this->authConfigPath;
    }

    /**
     * @param Yaml $yaml
     */
    private function loadConfig(Yaml $yaml): void
    {
        $array = $yaml->parseFile(self::CONFIG_PATH);
        $this->spreadsheetId = $array["spreadsheetId"];
        $this->cellRange = $array["cellRange"];
    }

    /**
     * @return string
     */
    public function getSpreadsheetId(): string
    {
        return $this->spreadsheetId;
    }

    /**
     * @return string
     */
    public function getCellRange(): string
    {
        return $this->cellRange;
    }
}