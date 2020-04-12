<?php declare(strict_types=1);

namespace Yaspcc\Ratings\Service;

use Yaspcc\Ratings\Entity\Submission;
use Yaspcc\Ratings\Repository\ProtonDBRepository;
use Yaspcc\Steam\Entity\Game;
use Yaspcc\Steam\Entity\User\Profile;

class ProtonDBService implements RatingServiceInterface
{
    /**
     * @var ProtonDBRepository
     */
    private $repository;

    private $ratingTypes = [
        'Borked' => 0,
        'Bronze' => 1,
        'Silver' => 2,
        'Gold' => 3,
        'Platinum' => 4
    ];


    public function __construct(ProtonDBRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getGameRatings(int $gameId): ?array
    {
        return $this->repository->getRating($gameId);
    }

    public function getAllRatings(): array
    {
        return $this->repository->getAll();
    }

    /**
     * @param array $games
     * @param array $ratings
     * @param Profile[] $profiles
     * @return array
     */
    public function matchGamesToRatings(array $games, array $ratings, array $profiles): array
    {
        $gameRatings = [];
        foreach ($games as $game) {
            $played = 0;
            foreach ($profiles as $profile) {
                if (!empty($profile->games[$game->id])) {
                    $played += $profile->games[$game->id]->getPlaytime() ?? 0;
                }
            }
            $gameRatings[] = ["info" => $games[$game->id], "ratings" => $ratings[$game->id] ?? [], "played" => $played];
        }

        return $gameRatings;
    }

    /**
     * @param Game[]|int[] $games
     * @return Submission[]
     */
    public function getRatingsByArray(array $games): array
    {
        return $this->repository->getRatings($games);
    }

    /**
     * @param Submission[] $ratings
     * @return string
     */
    public function calculateAverageRating(array $ratings): string
    {
        $total = 0;

        foreach ($ratings as $submission) {
            $total += $this->ratingTypes[$submission->getRating()] ?? 0;
        }

        $average = round($total / count($ratings),PHP_ROUND_HALF_UP);

        return array_search(number_format($average,0), $this->ratingTypes);
    }
}