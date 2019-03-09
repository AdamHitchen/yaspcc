<?php declare(strict_types=1);

namespace Yaspcc\Ratings\Config;

use Symfony\Component\Yaml\Yaml;

/**
 * Class GoogleSheetsConfig
 * @package Yaspcc\Steam\GoogleSheetsConfig
 */
class GoogleSheetsConfig
{
    const CONFIG_PATH = __DIR__ . "/config/google.yaml";

    /** @var string */
    private $spreadsheetId;
    /** @var string */
    private $cellRange;
    /** @var string */
    private $authConfigPath = ROOT_DIR . "config/googlecreds.json";

    /**
     * GoogleSheetsConfig constructor.
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