<?php declare(strict_types=1);

namespace Yaspcc\Api;

use Psr\Log\LoggerInterface;
use Yaspcc\Cache\KeyValueCacheInterface;
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
     * @var KeyValueCacheInterface
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
     * @param KeyValueCacheInterface $cache
     * @param LoggerInterface $logger
     * @param RatingServiceInterface $ratingService
     */
    public function __construct(
        SteamService $steamService,
        KeyValueCacheInterface $cache,
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
    public function getProfileRatings($userId): string
    {
        $profile = $this->steamService->getProfile($userId);
        $profileGames = $this->getProfileGames($profile);
        $matches = $this->getRatingMatches($profileGames);

        return json_encode($matches);
    }

    /**
     * @param array $profileGames
     * @return array
     */
    private function getRatingMatches(array $profileGames): array
    {
        $matches = [];
        foreach($profileGames as $game) {
            $match = $this->ratingService->getGameRatings($game->id);
            if(!empty($match)) {
                $matches[$game->id] = $match;
            }
        }
        return $matches;
    }

    /**
     * @param Profile $profile
     * @return array
     */
    private function getProfileGames(Profile $profile): array
    {
        $games = [];
        $ignored = $this->steamService->getIgnoreList();
        /** @var Game $game */
        foreach($profile->games as $game) {
            if(!in_array($game->appid,$ignored)) {
                try {
                    $games[]=$this->steamService->getGame($game->appid);
                } catch (\Exception $exception) {
                    $this->logger->alert("Game in user profile not found: " . $game->appid);
                }
            }
        }
        return  $games;
    }

}