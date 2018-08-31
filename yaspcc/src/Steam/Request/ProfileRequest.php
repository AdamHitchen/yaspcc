<?php declare(strict_types=1);

namespace Yaspcc\Steam\Request;

use GuzzleHttp\Client;
use Yaspcc\Steam\Config\Config;
use Yaspcc\Steam\Entity\User\Profile;

class ProfileRequest
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
     * @param string $userId
     * @return Profile
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserProfile(string $userId): Profile
    {
        $userId = $this->getIdByUsername($userId) ?? $userId;

        $result = $this->httpClient->request(
            'GET',
            $this->config->getBaseUrl() . "IPlayerService/GetOwnedGames/v0001/?key=" . $this->config->getApiKey() . "&steamid=" . $userId . "&format=json"
        );
        $response = json_decode($result->getBody()->getContents());

        $profile = (new Profile($userId))->fromJson($response);

        return $profile;
    }

    private function getIdByUsername(string $username): ?string
    {
        try {
            $result = $this->httpClient->request(
                'GET',
                $this->config->getBaseUrl() . "ISteamuser/ResolveVanityUrl/v0001/?key=" . $this->config->getApiKey() . "&vanityurl=" . $username
            );
            $response = json_decode($result->getBody()->getContents());
            if ($response->response->success === 1) {
                return $response->response->steamid;
            }

        } catch (\Exception $exception) {
            if ($exception->getCode() == 404) {
                return null;
            }

        }
        return null;
    }

}