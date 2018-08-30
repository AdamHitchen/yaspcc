<?php declare(strict_types=1);

namespace Yaspcc\Controller;

use Symfony\Component\Routing\Annotation\Route;

class TestController
{


    /**
     * Matches /test exactly
     *
     * @Route("/test", name="test_list")
     */
    public function list()
    {
        echo("hi :)");
    }

    public function __construct()
    {
        echo "hi";
    }
}
