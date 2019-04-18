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
    /** @var string|null */
    public $imageUrl;
    /** @var bool */
    public $isMultiplayer;


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
        $this->isMultiplayer = false;

        if($objData->categories) {
            foreach($objData->categories as $category) {
                // Multi-player / Cross-Platform Multiplayer categories
                if($category->id === 1 || $category->id == 27) {
                    $this->isMultiplayer = true;
                    break;
                }
            }
        }
        return $this;
    }

    public function fromJson(\stdClass $jsonObj): Game
    {
        $this->isLinuxNative = $jsonObj->isLinuxNative;
        $this->isMultiplayer = $jsonObj->isMultiplayer;
        $this->hasCompleteData = $jsonObj->hasCompleteData;
        $this->imageUrl = $jsonObj->imageUrl;
        return $this;
    }
}