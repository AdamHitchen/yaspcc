<?php declare(strict_types=1);

namespace Yaspcc\Steam\Service;

use GuzzleHttp\Exception\BadResponseException;
use Psr\Log\LoggerInterface;
use Yaspcc\Steam\Entity\Game;
use Yaspcc\Steam\Entity\User\Profile;
use Yaspcc\Steam\Exception\UserNotFoundException;
use Yaspcc\Steam\Repository\GameRepository;
use Yaspcc\Steam\Repository\ProfileRepository;

class SteamService
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var GameRepository
     */
    private $gameRepository;
    /**
     * @var ProfileRepository
     */
    private $profileRepository;

    /**
     * SteamService constructor.
     * @param GameRepository $gameRepository
     * @param ProfileRepository $profileRepository
     * @param LoggerInterface $logger
     */
    public function __construct
    (
        GameRepository $gameRepository,
        ProfileRepository $profileRepository,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->gameRepository = $gameRepository;
        $this->profileRepository = $profileRepository;
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
            $profile = $this->profileRepository->get($steamId);
        } catch (BadResponseException $exception) {
            $this->logger->alert("Error while trying to get profile in SteamService: " . $exception->getMessage());
        }

        if (!isset($profile)) {
            throw new UserNotFoundException("Unable to find User.");
        }

        return $profile;
    }

    public function getIgnoreList(): array
    {
        return $this->gameRepository->getIgnoreList();
    }

    /**
     * @param int $gameId
     * @return Game
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Yaspcc\Steam\Exception\GameNotFoundException
     * @throws \Yaspcc\Steam\Exception\NoGameDataException
     */
    public function getGame(int $gameId): Game
    {
        return $this->gameRepository->get($gameId);
    }

}