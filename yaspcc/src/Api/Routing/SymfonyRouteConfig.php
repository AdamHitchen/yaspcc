<?php declare(strict_types=1);


namespace Yaspcc\Api\Routing;


use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Yaspcc\Api\Controller\ProfileController;

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
        $routes->add('', new Route($prefix.'profile/{id}',[
           '_controller' => [ProfileController::class, 'list']
        ]));
    }


    public function getRoutes()
    {
        return $this->routeCollection;
    }
}