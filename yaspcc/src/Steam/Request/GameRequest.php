<?php declare(strict_types=1);

namespace Yaspcc\Steam\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Yaspcc\Steam\Config\Config;
use Yaspcc\Steam\Entity\Game;
use Yaspcc\Steam\Entity\User\Profile;
use Yaspcc\Steam\Exception\ApiLimitExceededException;
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
     * @param int $gameId
     * @return Game
     * @throws NoGameDataException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getGame(int $gameId): Game
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
     * @throws ApiLimitExceededException
     * @throws GameNotFoundException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getGameByStoreApi(int $gameId): Game
    {
        try {
            $result = $this->httpClient->request(
                'GET',
                "http://store.steampowered.com/api/appdetails/?appids=" . $gameId
            );
        } catch (ClientException $exception) {
            throw new ApiLimitExceededException();
        }

        $response = json_decode($result->getBody()->getContents());

        //The store api returns an object with the ID as a child - can't access numeric children directly in PHP
        $obj = get_object_vars($response)[$gameId];
        if ($obj->success) {
            $game = new Game($obj->data->name, $gameId);
            return $game->fromJsonRequestObject($obj);
        } else {
            if (empty($obj)) {
                throw new GameNotFoundException();
            }
        }

        throw new GameNotFoundException();
    }

    public function getAllApps(): array
    {
        $result = $this->httpClient->request(
            'GET',
            'http://api.steampowered.com/ISteamApps/GetAppList/v0002/?key=' . $this->config->getApiKey() . '&format=json'
        );
        return json_decode($result->getBody()->getContents(), true);
    }
}