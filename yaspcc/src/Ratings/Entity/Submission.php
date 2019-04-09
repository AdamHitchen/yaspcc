<?php declare(strict_types=1);

namespace Yaspcc\Ratings\Entity;

class Submission
{
    /**
     * @var string
     */
    private $submitDate;
    /**
     * @var string
     */
    private $rating;
    /**
     * @var string
     */
    private $status;
    /**
     * @var string|null
     */
    private $notes;
    /**
     * @var string|null
     */
    private $distro;
    /**
     * @var string|null
     */
    private $driver;
    /**
     * @var string|null
     */
    private $specs;


    /**
     * Submission constructor.
     * @param $submitDate
     * @param $rating
     * @param $status
     * @param $notes
     * @param $distro
     * @param $driver
     * @param $specs
     */
    public function __construct(
        string $submitDate,
        string $rating,
        string $status,
        ?string $notes = null,
        ?string $distro = null,
        ?string $driver = null,
        ?string $specs = null
    ) {

        $this->submitDate = $submitDate;
        $this->rating = $rating;
        $this->status = $status;
        $this->notes = $notes;
        $this->distro = $distro;
        $this->driver = $driver;
        $this->specs = $specs;
    }
}