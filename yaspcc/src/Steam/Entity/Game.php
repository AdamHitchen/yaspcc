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
    /** @var int */
    public $id;
    /** @var bool */
    public $hasCompleteData;
    /** @var bool */
    public $isLinuxNative;
    /** @var string */
    public $imageUrl;

    /**
     * Game constructor.
     * @param int $id
     * @param string $name
     */
    public function __construct(string $name, int $id)
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
    public function fromJsonRequestObject(\stdClass $object): Game
    {
        $objData = $object->data;
        $this->hasCompleteData = true;
        $this->isLinuxNative = $objData->platforms->linux ?? false;
        $this->imageUrl = $objData->header_image;

        return $this;
    }

    public function fromJson(\stdClass $jsonObj): Game
    {
        $this->isLinuxNative = $jsonObj->isLinuxNative;
        $this->hasCompleteData = $jsonObj->hasCompleteData;
        $this->imageUrl = $jsonObj->imageUrl;
        return $this;
    }
}