<?php declare(strict_types=1);

namespace Yaspcc\Ratings\Service;

use Yaspcc\Ratings\Entity\Submission;

interface RatingServiceInterface
{
    function getAllRatings(): array;
    function getGameRatings(int $gameId): ?array;

    /**
     * @param array $games
     * @return Submission[]
     */
    function getRatingsByArray(array $games): array;

    function calculateAverageRating(array $ratings): string;
}