<?php declare(strict_types=1);

namespace Yaspcc\Ratings\Service;

interface RatingServiceInterface
{
    function getAllRatings(): array;
    function getGameRatings(int $gameId): ? \stdClass;
}