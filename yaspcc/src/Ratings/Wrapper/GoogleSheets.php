<?php declare(strict_types=1);

namespace Yaspcc\Ratings\Wrapper;

use Yaspcc\Ratings\Config\GoogleSheetsConfig as Config;

/**
 * Class GoogleSheets
 * @package Yaspcc\Ratings\Wrapper
 */
class GoogleSheets
{
    /** @var Config */
    private $config;
    /** @var \Google_Client */
    private $client;
    /** @var \Google_Service_Sheets */
    private $sheet;

    /**
     * GoogleSheets constructor.
     * @param Config $config
     * @param \Google_Client $client
     * @throws \Google_Exception
     */
    public function __construct(
        Config $config,
        \Google_Client $client
    ) {
        $this->config = $config;
        $this->client = $client;
        $this->initializeClient();
    }

    /**
     * @throws \Google_Exception
     */
    private function initializeClient():void
    {
        $this->client->setApplicationName("Yaspcc");
        $this->client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $this->client->setAccessType('offline');
        $this->client->setAuthConfig($this->config->getAuthPath());
        $this->sheet = new \Google_Service_Sheets($this->client);
    }

    public function getSheetArray(): array
    {
        return $this->sheet->spreadsheets_values->get(
            $this->config->getSpreadsheetId(),
            $this->config->getCellRange(),
            ['majorDimension' => 'ROWS']
        )->values;
    }

}