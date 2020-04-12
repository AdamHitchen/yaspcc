<?php declare(strict_types=1);

namespace Yaspcc\Ratings\Repository;

use Psr\Log\LoggerInterface;
use Yaspcc\Cache\CacheServiceInterface;
use Yaspcc\Ratings\Entity\Submission;

class ProtonDBRepository
{

    /**
     * @var CacheServiceInterface
     */
    private $cache;

    public function __construct(CacheServiceInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getAll(): array
    {
        if ($this->cache->exists("rating:timeout")) {
            $json = $this->cache->get("rating:all");
            $obj = json_decode($json,true);
            return $obj;
        }

        throw new \Exception("No data found - please run data population cron from ProtonDB data dump");
    }

    /**
     * @param array $array
     */
    public function setIndividualRatings(array $array): void
    {
        foreach ($array as $key => $value) {
            $this->cache->set("rating:" . $key, json_encode($value));
        }
    }

    /**
     * @param array $gameIds
     * @return Submissiona
     */
    public function getRatings(array $gameIds): array
    {
        $keys = [];

        foreach($gameIds as $gameId) {
            if(is_numeric($gameId)) {
                $keys[]=  "rating:" . $gameId;
                continue;
            }

            $keys[]=  "rating:" . $gameId->id;
        }

        $games = $this->cache->getMany($keys);
        $gameSubmissions = [];

        foreach($games as $key => $game) {
            $submissions = [];

            if(is_null($game)) {
                continue;
            }

            $gameRatings = json_decode($game);

            foreach($gameRatings as $rating) {
                if(is_string($rating)) {
                    $rating = json_decode($rating);
                }

                $submissions[] = new Submission(
                    $rating->submitDate,
                    $rating->rating,
                    $rating->rating,
                    $rating->notes,
                    $rating->os,
                    $rating->gpuDriver,
                    $rating->specs,
                    $rating->protonVersion,
                    $rating->kernel,
                    $rating->cpu,
                    $rating->ram,
                    $rating->duration
                );
            }
            $gameSubmissions[str_replace("rating:","",$keys[$key])] = $submissions;
        }

        return $gameSubmissions;
    }

    /**
     * @param $gameId
     * @return mixed|null
     */
    public function getRating(int $gameId)
    {
        if($this->cache->exists("rating:".$gameId)) {
            $ratings = json_decode($this->cache->get("rating:".$gameId));

            $submissions = [];

            foreach($ratings as $rating) {

                if(is_string($rating)) {
                    $rating = json_decode($rating);
                }

                $submissions[] = new Submission(
                    $rating->submitDate,
                    $rating->rating,
                    $rating->rating,
                    $rating->notes,
                    $rating->os,
                    $rating->gpuDriver,
                    $rating->specs,
                    $rating->protonVersion,
                    $rating->kernel,
                    $rating->cpu,
                    $rating->ram,
                    $rating->duration
                );
            }

            return $submissions;
        }

        return null;
    }
}