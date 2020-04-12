<?php declare(strict_types=1);

namespace Yaspcc\Api\Controller;

use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Yaspcc\Api\ProfileRatingRequest;
use Yaspcc\Ratings\Service\RatingServiceInterface;
use Yaspcc\Steam\Exception\UserNotFoundException;
use Yaspcc\Steam\Service\SteamService;

class ProfileController
{
    /**
     * @var ProfileRatingRequest
     */
    private $profileRatingRequest;
    /**
     * @var SteamService
     */
    private $steamService;
    /**
     * @var RatingServiceInterface
     */
    private $ratingService;

    public function list(string $id): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        try {
            $profileRatings = $this->profileRatingRequest->getProfileRatings($id);
        } catch (UserNotFoundException $e) {
            $response->setStatusCode(400)
                ->setContent('{"error" : "Steam profile not found. Please check your profile is public" }');
            return $response;
        } catch (\throwable $e) {
            $response->setStatusCode(500)
                ->setContent('{"error" : "Something went wrong while contacting the server" }');
            return $response;
        }

        return $response->setStatusCode(200)->setContent($profileRatings);
    }

    public function compare(string $profileIds): Response
    {
        //Remove trailing slash
        $idsNoTrailingSlash = preg_replace('@/$@', "", $profileIds);
        $ids = explode('/', $idsNoTrailingSlash);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        try {
            [$matchedGames, $profiles] = $this->profileRatingRequest->getCommonGames($ids);
            $ratings = $this->ratingService->getRatingsByArray($matchedGames);
            $gameRatings = $this->ratingService->matchGamesToRatings($matchedGames, $ratings);

        } catch (UserNotFoundException $e) {
            $response->setStatusCode(400)
                ->setContent(new Error("Steam profile not found. Please check your profile is public"));
            return $response;
        } catch (NoGamesException $e) {
            $response->setStatusCode(400)
                ->setContent(new Error("No games found in one of the provided accounts"));
            return $response;
        } catch (\throwable $e) {
            $response->setStatusCode(500)
                ->setContent(new Error("Something went wrong while contacting the server"));
            return $response;
        }

        return $response->setStatusCode(200)->setContent(json_encode($gameRatings));
    }

    /**
     * ProfileController constructor.
     * @param ProfileRatingRequest $profileRatingRequest
     * @param SteamService $steamService
     */
    public function __construct(
        ProfileRatingRequest $profileRatingRequest,
        SteamService $steamService,
        RatingServiceInterface $ratingService
    ) {
        $this->profileRatingRequest = $profileRatingRequest;
        $this->steamService = $steamService;
        $this->ratingService = $ratingService;
    }
}
