<?php declare(strict_types=1);

namespace Yaspcc\Ratings\Repository;

use Yaspcc\Cache\CacheServiceInterface;
use Yaspcc\Ratings\Entity\Submission;
use Yaspcc\Ratings\Wrapper\GoogleSheets;

class GoogleSheetsRepository
{
    /**
     * @var GoogleSheets
     */
    private $sheets;
    /**
     * @var CacheServiceInterface
     */
    private $cache;

    /**
     * GoogleSheetsRepository constructor.
     * @param GoogleSheets $sheets
     * @param CacheServiceInterface $cache
     */
    public function __construct(
        GoogleSheets $sheets,
        CacheServiceInterface $cache
    ) {
        $this->sheets = $sheets;
        $this->cache = $cache;
    }

    /**
     * @return array
     */
    private function getAllFromCache(): array
    {
        $json = $this->cache->get("rating:all");
        $obj = json_decode($json,true);
        return $obj;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        if ($this->cache->exists("rating:timeout")) {
            return $this->getAllFromCache();
        }

        $baseArray = $this->sheets->getSheetArray();
        $sortedArray = [];
        foreach ($baseArray as $item) {
            if (empty($sortedArray[$item[0]])) {
                $sortedArray[$item[0]] = [];
                $sortedArray[$item[0]]["name"] = $item[1];
                $sortedArray[$item[0]]["submissions"] = [];
            }
            $sortedArray[$item[0]]["submissions"][] = new Submission(
                $item[2],
                $item[3],
                $item[4],
                $item[5],
                $item[6],
                $item[7],
                $item[8]
            );
        }

        $this->cache->set("rating:all", json_encode($sortedArray));
        $this->cache->set("rating:timeout","",1*60*60*24);
        $this->setIndividualRatings($sortedArray);

        return $sortedArray;
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
     * @param $gameId
     * @return mixed|null
     */
    public function getRating(int $gameId)
    {
        if($this->cache->exists("rating:".$gameId)) {
            return json_decode($this->cache->get("rating:".$gameId));
        } else if ($this->cache->exists("rating:timeout")) {
            return null;
        }

        return $this->getAll()[$gameId];
    }
}