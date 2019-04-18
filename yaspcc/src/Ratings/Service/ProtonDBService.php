<?php declare(strict_types=1);

namespace Yaspcc\Ratings\Service;

use Yaspcc\Ratings\Entity\Submission;
use Yaspcc\Ratings\Repository\ProtonDBRepository;
use Yaspcc\Steam\Entity\Game;

class ProtonDBService implements RatingServiceInterface
{
    /**
     * @var ProtonDBRepository
     */
    private $repository;

    public function __construct(ProtonDBRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getGameRatings(int $gameId): ? array
    {
        return $this->repository->getRating($gameId);
    }

    public function getAllRatings(): array
    {
        return $this->repository->getAll();
    }

    /**
     * @param Game[]|int[] $games
     * @return Submission[]
     */
    public function getRatingsByArray(array $games): array
    {
        return $this->repository->getRatings($games);
    }
}