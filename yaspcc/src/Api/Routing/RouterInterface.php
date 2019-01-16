<?php declare(strict_types=1);

namespace Yaspcc\Api\Routing;

interface RouterInterface
{
    /**
     * @return mixed
     */
    function match();
}