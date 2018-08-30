<?php declare(strict_types=1);


namespace Yaspcc\Routing;


interface RouterInterface
{
    /**
     * @return mixed
     */
    function match();
}