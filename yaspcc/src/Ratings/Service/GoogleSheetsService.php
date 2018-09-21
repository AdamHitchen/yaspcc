<?php declare(strict_types=1);

namespace Yaspcc\Ratings\Service;

use Yaspcc\Ratings\Repository\GoogleSheetsRepository;

/**
 * Class RatingService
 * @package Yaspcc\Ratings\Service
 */
class GoogleSheetsService implements RatingServiceInterface
{
    /**
     * @var GoogleSheetsRepository
     */
    private $repository;

    /**
     * GoogleSheetsService constructor.
     * @param GoogleSheetsRepository $repository
     */
    public function __construct(
        GoogleSheetsRepository $repository
    ) {
        $this->repository = $repository;
    }

    public function getGameRatings(int $gameId): ? \stdClass
    {
        return $this->repository->getRating($gameId);
    }

    public function getAllRatings(): array
    {
        return $this->repository->getAll();
    }

}