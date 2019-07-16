<?php declare(strict_types=1);

namespace Yaspcc\Api;

use Psr\Log\LoggerInterface;
use Yaspcc\Cache\CacheServiceInterface;
use Yaspcc\Ratings\Exception\NoGamesException;
use Yaspcc\Ratings\Service\RatingServiceInterface;
use Yaspcc\Steam\Entity\User\Game;
use Yaspcc\Steam\Entity\User\Profile;
use Yaspcc\Steam\Service\SteamService;

class ProfileRatingRequest
{
    /**
     * @var SteamService
     */
    private $steamService;
    /**
     * @var CacheServiceInterface
     */
    private $cache;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var RatingServiceInterface
     */
    private $ratingService;

    /**
     * ProfileRatingRequest constructor.
     * @param SteamService $steamService
     * @param CacheServiceInterface $cache
     * @param LoggerInterface $logger
     * @param RatingServiceInterface $ratingService
     */
    public function __construct(
        SteamService $steamService,
        CacheServiceInterface $cache,
        LoggerInterface $logger,
        RatingServiceInterface $ratingService
    ) {
        $this->steamService = $steamService;
        $this->cache = $cache;
        $this->logger = $logger;
        $this->ratingService = $ratingService;
    }

    /**
     * @param $userId
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Yaspcc\Steam\Exception\UserNotFoundException
     */
    public function getProfileRatings(string $userId): string
    {
        $profile = $this->steamService->getProfile($userId);
        $profileGames = $this->getProfileGames($profile);

        $ratings = $this->ratingService->getRatingsByArray($profileGames);
        $gamesRatings = $this->ratingService->matchGamesToRatings($profileGames, $ratings);

        return json_encode($gamesRatings);
    }

    /**
     * @param array $userIds
     * @return \Yaspcc\Steam\Entity\Game[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Yaspcc\Steam\Exception\UserNotFoundException
     */
    public function getCommonGames(array $userIds): array
    {
        if (count($userIds) < 2) {
            throw new \Exception("Only one profile provided.");
        }

        /** @var Profile[] $profiles */
        $profiles = [];

        $smallestProfileCount = PHP_INT_MAX;
        $smallestProfileId = 0;

        foreach($userIds as $userId) {
            $profiles[$userId]= $this->steamService->getProfile($userId);

            //Get the smallest library so we can loop through fewer games.
            if(count($profiles[$userId]->games) < $smallestProfileCount) {
                $smallestProfileCount = count($profiles[$userId]->games);
                $smallestProfileId = $userId;
            }
        }

        $matches = [];
        /** @var Game[] $games */
        $games = $profiles[$smallestProfileId]->games;

        if($smallestProfileCount === 0) {
            throw new NoGamesException(sprintf("User %s has no games or profile is hidden", $smallestProfileId));
        }

        foreach ($games as $game) {
            foreach($profiles as $profile) {
                if(!array_key_exists($game->appid, $profile->games)) {
                    continue 2;
                }
            }

            try {
                //$matches[$game->getAppId()]= $this->steamService->getGame($game->getAppid());
                $matches[$game->getAppId()]= $game->getAppid();
            } catch (\throwable $t) {
                $this->logger->alert("Game not found: " . $game->getAppid());
            }
        }

        $results = $this->steamService->getGames($matches);


        return $results;
    }

    /**
     * @param array $profileGames
     * @return array
     */
    private function getRatingMatches(array $profileGames): array
    {
        $matches = [];

        foreach ($profileGames as $game) {
            $match = $this->ratingService->getGameRatings($game->id);
            if (!empty($match)) {
                $matches[$game->id] = $match;
            }
        }
        return $matches;
    }

    /**
     * @param Profile $profile
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getProfileGames(Profile $profile): array
    {
        $gameIds = [];
        $ignored = $this->steamService->getIgnoredGames();
        /** @var Game $game */
        foreach ($profile->games as $game) {
            if (!in_array($game->getAppid(), $ignored)) {
                $gameIds[]= $game->getAppid();
            }
        }

        return $this->steamService->getGames($gameIds);
    }

}