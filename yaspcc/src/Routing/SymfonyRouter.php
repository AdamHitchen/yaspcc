<?php declare(strict_types=1);


namespace Yaspcc\Routing;


use DI\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;

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
        $res = $matcher->match($request->getPathInfo());
        $result = call_user_func([
            $this->container->make($res["_controller"][0]),
            $res["_controller"][1]
        ]);
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