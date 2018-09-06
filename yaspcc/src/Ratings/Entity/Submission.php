<?php declare(strict_types=1);

namespace Yaspcc\Ratings\Entity;

class Submission
{
    public $submitDate;
    public $rating;
    public $status;
    public $notes;
    public $distro;
    public $driver;
    public $specs;

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
        $submitDate,
        $rating,
        $status,
        $notes = null,
        $distro = null,
        $driver = null,
        $specs = null
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