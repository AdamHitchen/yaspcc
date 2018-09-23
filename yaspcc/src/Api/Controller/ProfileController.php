<?php declare(strict_types=1);

namespace Yaspcc\Api\Controller;

use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Yaspcc\Api\ProfileRatingRequest;
use Yaspcc\Steam\Exception\UserNotFoundException;

class ProfileController
{
    /**
     * @var ProfileRatingRequest
     */
    private $profileRatingRequest;


    /**
     * Matches /test exactly
     *
     * @Route("/test", name="test_list")
     */
    public function list($id)
    {

        $response = new Response();
        $response->headers->set('Content-Type','application/json');
        try {
            $profileRatings = $this->profileRatingRequest->getProfileRatings($id);
        } catch (GuzzleException $e) {
            $response->setStatusCode(500)
                ->setContent('{"error" : "Something went wrong while contacting the server" }')
                ->send();
            return;
        } catch (UserNotFoundException $e) {
            $response->setStatusCode(400)
                ->setContent('{"error" : "Steam profile not found. Please check your profile is public" }')
                ->send();
            return;
        }

        $response->setStatusCode(200);
        $response->setContent($profileRatings);
        $response->send();
    }

    /**
     * ProfileController constructor.
     * @param SteamService $steamService
     * @param RatingServiceInterface $ratingService
     */
    public function __construct(ProfileRatingRequest $profileRatingRequest)
    {

        $this->profileRatingRequest = $profileRatingRequest;
    }
}
