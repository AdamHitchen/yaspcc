<?php declare(strict_types=1);

namespace Yaspcc\Cron;

use Psr\Log\LoggerInterface;
use Yaspcc\Cache\CacheServiceInterface;
use Yaspcc\Ratings\Entity\Submission;
use Yaspcc\Ratings\Service\RatingServiceInterface;
use Yaspcc\Steam\Repository\GameRepository;

class UpdateProtonDBRatings
{
    /**
     * @var CacheServiceInterface
     */
    private $cacheService;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var RatingServiceInterface
     */
    private $ratingService;
    /**
     * @var GameRepository
     */
    private $gameRepository;

    /**
     * UpdateProtonDBRatings constructor.
     * @param CacheServiceInterface $cacheService
     * @param LoggerInterface $logger
     */
    public function __construct(
        CacheServiceInterface $cacheService,
        RatingServiceInterface $ratingService,
        LoggerInterface $logger,
        GameRepository $gameRepository
    ) {
        $this->cacheService = $cacheService;
        $this->logger = $logger;
        $this->ratingService = $ratingService;
        $this->gameRepository = $gameRepository;
    }

    private function boolToNull($string): ?string
    {
        return getType($string) === 'boolean' ? null : $string;
    }

    /**
     * Some fields are returned as "false" when they just shouldn't exist.
     * In this case we set them to null so they can be handled similarly to if they were not present.
     */
    private function sanitizeRating(\stdClass $report): \stdClass
    {
        $report->cpu = $this->boolToNull($report->cpu);
        $report->gpuDriver = $this->boolToNull($report->gpuDriver);
        $report->gpu = $this->boolToNull($report->gpu);
        $report->ram = $this->boolToNull($report->ram);
        $report->notes = $this->boolToNull($report->notes);
        $report->kernel = $this->boolToNull($report->kernel);
        $report->os = $this->boolToNull($report->os);
        $report->duration = $this->boolToNull($report->duration);

        return $report;
    }

    public function run($dir)
    {
        $filepath = $dir . '/var/import/reports_piiremoved.json';

        if (!file_exists($filepath)) {
            return;
        }

        $json = file_get_contents($filepath);
        $ratings = json_decode($json);

        $apps = [];

        foreach ($ratings as $key => $rating) {
            $rating = $this->sanitizeRating($rating);

            if (!$rating->rating || getType($rating->appId) != "string") {
                continue;
            }

            $timestamp = (int)$rating->timestamp;

            try {
                $submission = new Submission(
                    (new \DateTime())->setTimestamp($timestamp)->format(DATE_ATOM),
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
            } catch (\Throwable $t) {
                $this->logger->error("Issue with ratings for game " . $rating->appId);
                continue;
            }

            if (empty($apps[$rating->appId])) {
                $apps[$rating->appId] = [];
            }

            $apps[$rating->appId][] = $submission;
        }

        foreach ($apps as $appid => $ratings) {
            //Some apps in the exported data have no APP ID
            if (!is_numeric($appid)) {
                continue;
            }

            try {
                $averageRating = $this->ratingService->calculateAverageRating($ratings);
                $this->cacheService->set('game:' . $appid . ':average', $averageRating);
            } catch (\Exception $e) {
                $this->logger->alert("Could not update ratings for game with ID " . $appid . " with error: " . $e->getMessage());
            }

            $this->cacheService->set('rating:' . $appid, json_encode($ratings));
        }

        unlink($filepath);
    }
}