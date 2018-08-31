<?php declare(strict_types=1);

namespace Yaspcc\Steam\Service;

use GuzzleHttp\Exception\BadResponseException;
use Psr\Log\LoggerInterface;
use Yaspcc\Steam\Entity\Game;
use Yaspcc\Steam\Entity\User\Profile;
use Yaspcc\Steam\Exception\UserNotFoundException;
use Yaspcc\Steam\Request\GameRequest;
use Yaspcc\Steam\Request\ProfileRequest;

class SteamService
{
    /**
     * @var GameRequest
     */
    private $gameRequest;
    /**
     * @var ProfileRequest
     */
    private $profileRequest;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SteamService constructor.
     * @param GameRequest $gameRequest
     * @param ProfileRequest $profileRequest
     * @param LoggerInterface $logger
     */
    public function __construct
    (
        GameRequest $gameRequest,
        ProfileRequest $profileRequest,
        LoggerInterface $logger
    ) {

        $this->gameRequest = $gameRequest;
        $this->profileRequest = $profileRequest;
        $this->logger = $logger;
    }

    /**
     * @param string $steamId
     * @return Profile
     * @throws UserNotFoundException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getProfile(string $steamId): Profile
    {
        try {
            $profile = $this->profileRequest->getUserProfile($steamId);
        } catch (BadResponseException $exception) {
            $this->logger->alert($exception->getMessage());
        }

        if (empty($profile)) {
            throw new UserNotFoundException("Unable to find User.");
        }

        return $profile;
    }

    /**
     * @param string $gameId
     * @return Game
     */
    public function getGame(string $gameId) : Game
    {
        $game = $this->gameRequest->getGameByStoreApi(420);
        return $game;
    }

}