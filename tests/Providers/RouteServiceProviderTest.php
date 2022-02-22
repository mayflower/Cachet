<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Tests\Cachet\Providers;

use AltThree\TestBench\ServiceProviderTrait;
use CachetHQ\Cachet\Http\Middleware\Authenticate;
use CachetHQ\Cachet\Providers\RouteServiceProvider;
use CachetHQ\Tests\Cachet\AbstractTestCase;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;

/**
 * This is the route service provider test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class RouteServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTrait;

    /**
     * The login routes should always be available regardless of the always authenticate setting.
     */
    public function testWhenAlwaysAuthenticateIsEnabledLoginRoutesAreWhiteListed()
    {
        $loginRoutes = [
            'get:auth.login',
            'post:auth.login',
            'post:auth.two-factor',
            'get:auth.logout',
            'get:signup.invite',
            'post:signup.invite',
        ];

        $this->assertRoutesDontHaveAuthMiddleware($loginRoutes, $this->bootRouter(true));
    }

    /**
     * The setup routes should always be available regardless of the always authenticate setting.
     */
    public function testWhenAlwaysAuthenticateIsEnabledSetupRoutesAreWhiteListed()
    {
        $loginRoutes = [
            'get:setup',
            'post:setup.step1',
            'post:setup.step2',
            'post:setup.step3',
        ];

        $this->assertRoutesDontHaveAuthMiddleware($loginRoutes, $this->bootRouter(true));
    }

    /**
     * It's possible to retrieve the cachet version, status and ping endpoints regardless of the
     * always authenticate setting.
     */
    public function testWhenAlwaysAuthenticateIsEnabledApiSystemRoutesAreWhiteListed()
    {
        $routeActions = [
            'CachetHQ\Cachet\Http\Controllers\Api\GeneralController@ping',
            'CachetHQ\Cachet\Http\Controllers\Api\GeneralController@version',
            'CachetHQ\Cachet\Http\Controllers\Api\GeneralController@status',
        ];

        $router = $this->bootRouter(true);

        foreach ($routeActions as $routeAction) {
            $route = $router->getRoutes()->getByAction($routeAction);
            $this->assertInstanceOf(Route::class, $route);

            $middleware = $route->gatherMiddleware();
            $this->assertFalse(in_array('auth.api:true', $middleware, true));
        }
    }

    /**
     * When using always authenticate, normal graceful api routes will require full authentication.
     */
    public function testWhenAlwaysAuthenticateIsEnabledApiRoutesAreHardAuthenticated()
    {
        $routeActions = [
            'CachetHQ\Cachet\Http\Controllers\Api\ComponentController@index',
            'CachetHQ\Cachet\Http\Controllers\Api\ComponentGroupController@index',
            'CachetHQ\Cachet\Http\Controllers\Api\ComponentGroupController@show',
            'CachetHQ\Cachet\Http\Controllers\Api\ComponentController@show',
            'CachetHQ\Cachet\Http\Controllers\Api\IncidentController@index',
            'CachetHQ\Cachet\Http\Controllers\Api\IncidentController@show',
            'CachetHQ\Cachet\Http\Controllers\Api\IncidentUpdateController@index',
            'CachetHQ\Cachet\Http\Controllers\Api\IncidentUpdateController@show',
            'CachetHQ\Cachet\Http\Controllers\Api\MetricController@index',
            'CachetHQ\Cachet\Http\Controllers\Api\MetricController@show',
            'CachetHQ\Cachet\Http\Controllers\Api\MetricPointController@index',
            'CachetHQ\Cachet\Http\Controllers\Api\ScheduleController@index',
            'CachetHQ\Cachet\Http\Controllers\Api\ScheduleController@show',
        ];

        $router = $this->bootRouter(true);

        foreach ($routeActions as $routeAction) {
            $route = $router->getRoutes()->getByAction($routeAction);
            $this->assertInstanceOf(Route::class, $route);

            $middleware = $route->gatherMiddleware();
            $this->assertTrue(in_array('auth.api:true', $middleware, true));
        }
    }

    /**
     * When enabling the always authenticate setting, the core frontpage routes require authentication.
     */
    public function testWhenAlwaysAuthenticateIsEnabledAllNormalRoutesAreAuthenticated()
    {
        $namedRoutes = [
            'get:status-page',
            'get:incident',
            'get:schedule',
            'get:metric',
            'get:component_shield',
            'get:subscribe',
            'post:subscribe',
            'get:subscribe.manage',
            'post:subscribe.manage',
            'get:subscribe.verify',
            'get:subscribe.unsubscribe',
        ];

        $this->assertRoutesHaveAuthMiddleware($namedRoutes, $this->bootRouter(true));
    }

    /**
     * This test asserts that when always authenticate is disabled, you are allowed to visit the frontpage
     * routes without enforced authentication.
     */
    public function testWhenAlwaysAuthenticateIsDisabledAllNormalRoutesAreUnauthenticated()
    {
        $namedRoutes = [
            'get:status-page',
            'get:incident',
            'get:schedule',
            'get:metric',
            'get:component_shield',
            'get:subscribe',
            'post:subscribe',
            'get:subscribe.manage',
            'post:subscribe.manage',
            'get:subscribe.verify',
            'get:subscribe.unsubscribe',
        ];

        $this->assertRoutesDontHaveAuthMiddleware($namedRoutes, $this->bootRouter(false));
    }

    /**
     * A helper method that will execute the RouteProvider's map function and return a clean router.
     *
     * @param bool $alwaysAuthenticate
     *
     * @return Router
     */
    private function bootRouter($alwaysAuthenticate)
    {
        $this->app->config->set('setting.always_authenticate', $alwaysAuthenticate);
        $router = $this->app->make(Router::class);
        $router->setRoutes(new RouteCollection());

        $routeServiceProvider = new RouteServiceProvider($this->app);
        $routeServiceProvider->map($router);

        return $router;
    }

    /**
     * Assertion helper that asserts if the authentication middleware has not been injected onto
     * the collection of named routes.
     *
     * @param array  $routeNames
     * @param Router $router
     */
    private function assertRoutesDontHaveAuthMiddleware(array $routeNames, Router $router)
    {
        foreach ($routeNames as $routeName) {
            $route = $router->getRoutes()->getByName($routeName);
            $this->assertInstanceOf(Route::class, $route);

            $middleware = $route->gatherMiddleware();
            $this->assertFalse(in_array(Authenticate::class, $middleware, true));
        }
    }

    /**
     * Assertion helper that asserts if the authentication middleware has been injected onto
     * the collection of named routes.
     *
     * @param array  $routeNames
     * @param Router $router
     */
    private function assertRoutesHaveAuthMiddleware(array $routeNames, Router $router)
    {
        foreach ($routeNames as $routeName) {
            $route = $router->getRoutes()->getByName($routeName);
            $this->assertInstanceOf(Route::class, $route);

            $middleware = $route->gatherMiddleware();
            $this->assertTrue(in_array(Authenticate::class, $middleware, true));
        }
    }
}
