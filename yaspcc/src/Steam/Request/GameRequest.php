<?php declare(strict_types=1);

namespace Yaspcc\Steam\Request;

use GuzzleHttp\Client;
use Yaspcc\Steam\Config\Config;
use Yaspcc\Steam\Entity\Game;
use Yaspcc\Steam\Entity\User\Profile;
use Yaspcc\Steam\Exception\GameNotFoundException;
use Yaspcc\Steam\Exception\NoGameDataException;

class GameRequest
{
    /**
     * @var Client
     */
    private $httpClient;
    /**
     * @var Config
     */
    private $config;

    /**
     * ProfileRequest constructor.
     * @param Client $httpClient
     * @param Config $config
     */
    public function __construct(Client $httpClient, Config $config)
    {
        $this->httpClient = $httpClient;
        $this->config = $config;
    }

    /**
     * @param string $gameId
     * @return Game
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getGame(string $gameId): Game
    {
        $result = $this->httpClient->request(
            'GET',
            $this->config->getBaseUrl() . "ISteamUserStats/GetSchemaForGame/v2/?key=" . $this->config->getApiKey() . "&appid=" . $gameId . "&include_appinfo&format=json"
        );
        $response = json_decode($result->getBody()->getContents());

        if (empty($response->game->gameName)) {
            throw new NoGameDataException("Steam Api responded with no data.");
        }

        return new Game($response->game->gameName, $gameId);
    }

    /**
     * @param $gameId
     * @return Game
     */
    public function getGameByStoreApi($gameId): Game
    {
        $result = $this->httpClient->request(
            'GET',
            "http://store.steampowered.com/api/appdetails/?appids=" . $gameId
        );
        $response = json_decode($result->getBody()->getContents());
        $obj = get_object_vars($response)[$gameId];
        if ($obj->success) {
            $game = new Game($obj->gameName, $gameId);
            return $game->fromJsonObject($response);
        }

        throw new GameNotFoundException();
    }

}