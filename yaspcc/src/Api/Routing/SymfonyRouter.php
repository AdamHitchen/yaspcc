<?php declare(strict_types=1);

namespace Yaspcc\Api\Routing;

use DI\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class SymfonyRouter implements RouterInterface
{
    /**
     * @var SymfonyRouteConfig
     */
    private $routeConfig;
    /**
     * @var RequestContext
     */
    private $requestContext;
    /**
     * @var Container
     */
    private $container;

    /**
     * SymfonyRouter constructor.
     * @param SymfonyRouteConfig $routeConfig
     * @param RequestContext $requestContext
     * @param Container $container
     */
    public function __construct
    (
        SymfonyRouteConfig $routeConfig,
        RequestContext $requestContext,
        Container $container
    ) {

        $this->routeConfig = $routeConfig;
        $this->requestContext = $requestContext;
        $this->container = $container;
    }


    /**
     * @return mixed|void
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    function match()
    {
        $request = Request::createFromGlobals();

        $matcher = $this->getMatcher();
        $match = $matcher->match($request->getPathInfo());

        $controllerClass = $match["_controller"][0];
        $controllerFunction = $match["_controller"][1];
        array_shift($match);
        $result = call_user_func_array([
            $this->container->make($controllerClass),
            $controllerFunction
        ],$match);

    }

    /**
     * @return UrlMatcher
     */
    function getMatcher(): UrlMatcher
    {
        $routes = $this->routeConfig->getRoutes();
        return new UrlMatcher($routes, $this->requestContext);
    }

}