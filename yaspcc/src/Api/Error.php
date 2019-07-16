<?php declare(strict_types=1);

namespace Yaspcc\Api;

class Error
{
    /** @var string $error */
    public $error;

    public function __construct(string $error)
    {
        $this->error = $error;
    }


    public function __toString()
    {
        return $this->error;
    }
}