<?php declare(strict_types=1);

namespace Yaspcc\Ratings\Entity;

class Submission implements \JsonSerializable
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
     * @var string|null
     */
    private $protonVersion;
    /**
     * @var string|null
     */
    private $kernel;
    /**
     * @var string|null
     */
    private $cpu;
    /**
     * @var string|null
     */
    private $ram;
    /**
     * @var string|null
     */
    private $gpu;
    /**
     * @var string|null
     */
    private $duration;


    /**
     * Submission constructor.
     * @param string $submitDate
     * @param string $rating
     * @param string $status
     * @param string|null $notes
     * @param string|null $distro
     * @param string|null $driver
     * @param string|null $specs
     * @param string|null $protonVersion
     * @param string|null $kernel
     * @param string|null $cpu
     * @param string|null $ram
     * @param string|null $gpu
     * @param string|null $duration
     */
    public function __construct(
        string $submitDate,
        string $rating,
        string $status,
        ?string $notes = null,
        ?string $distro = null,
        ?string $driver = null,
        ?string $specs = null,
        ?string $protonVersion = null,
        ?string $kernel = null,
        ?string $cpu = null,
        ?string $ram = null,
        ?string $gpu = null,
        ?string $duration = null
    ) {

        $this->submitDate = $submitDate;
        $this->rating = $rating;
        $this->status = $status;
        $this->notes = $notes;
        $this->distro = $distro;
        $this->driver = $driver;
        $this->specs = $specs;
        $this->protonVersion = $protonVersion;
        $this->kernel = $kernel;
        $this->cpu = $cpu;
        $this->ram = $ram;
        $this->gpu = $gpu;
        $this->duration = $duration;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * @return string
     */
    public function getRating(): string
    {
        return $this->rating;
    }
}