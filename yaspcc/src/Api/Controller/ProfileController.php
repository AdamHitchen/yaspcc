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

    public function list(string $id) : Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        try {
            $profileRatings = $this->profileRatingRequest->getProfileRatings($id);
        } catch (GuzzleException $e) {
            $response->setStatusCode(500)
                ->setContent('{"error" : "Something went wrong while contacting the server" }');
            return $response;
        } catch (UserNotFoundException $e) {
            $response->setStatusCode(400)
                ->setContent('{"error" : "Steam profile not found. Please check your profile is public" }');
            return $response;
        }

        return $response->setStatusCode(200)->setContent($profileRatings);
    }

    /**
     * ProfileController constructor.
     * @param ProfileRatingRequest $profileRatingRequest
     */
    public function __construct(ProfileRatingRequest $profileRatingRequest)
    {

        $this->profileRatingRequest = $profileRatingRequest;
    }
}
