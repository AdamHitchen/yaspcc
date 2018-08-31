<?php declare(strict_types=1);

namespace Yaspcc\Steam\Entity;

/**
 * Class Game
 * @package Yaspcc\Steam\Entity
 */
class Game
{
    /** @var string */
    public $name;
    /** @var string */
    private $id;
    /** @var bool */
    private $hasCompleteData;
    /** @var bool */
    private $isLinuxNative;
    /** @var string */
    private $imageUrl;

    /**
     * Game constructor.
     * @param int $id
     * @param string $name
     */
    public function __construct(string $name, string $id)
    {
        $this->name = $name;
        $this->id = $id;
        $this->hasCompleteData = false;
        $this->isLinuxNative = false;
    }

    /**
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->hasCompleteData;
    }

    /**
     * @param \stdClass $object
     * @return Game
     */
    public function fromJsonObject(\stdClass $object): Game
    {
        $objData = $object->data;
        $this->hasCompleteData = true;
        $this->isLinuxNative = $objData->platforms->linux ?? false;
        $this->imageUrl = $objData->header_image;

        return $this;
    }
}