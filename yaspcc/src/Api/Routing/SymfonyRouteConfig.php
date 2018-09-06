<?php declare(strict_types=1);


namespace Yaspcc\Api\Routing;


use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Yaspcc\Api\Controller\TestController;

final class SymfonyRouteConfig
{
    /**
     * @var RouteCollection
     */
    private $routeCollection;

    /**
     * SymfonyRouteConfig constructor.
     * @param RouteCollection $routeCollection
     */
    public function __construct(RouteCollection $routeCollection)
    {
        $this->routeCollection = $routeCollection;
        $this->configure();
    }

    private function configure()
    {
        $routes = $this->routeCollection;
        $prefix = 'api/v1/';
        $routes->add('test', new Route($prefix.'test',[
           '_controller' => [TestController::class, 'list']
        ]));
    }


    public function getRoutes()
    {
        return $this->routeCollection;
    }
}