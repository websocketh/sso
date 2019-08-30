<?php
/**
 * Created by PatPat.
 * User: gan.huang
 * Date: 2019/8/30
 * Time: 16:17
 * Description
 */

namespace PatPat\Sso;

use Illuminate\Routing\Route;
class Permission
{

    public function getPermissions(){
        return $this->getRoutes();
    }

    private function getRoutes()
    {
        $list = \Illuminate\Support\Facades\Route::getRoutes();
        $routes = collect($list)->map(function ($route) {
            return $this->getRouteInformation($route);
        })->all();
        return array_filter($routes);
    }


    private function getRouteInformation(Route $route)
    {
        return $this->filterRoute([
            //'host'   => $route->domain(),
            'method' => implode('|', $route->methods()),
            'uri'    => $route->uri(),
            //'name'   => $route->getName(),
            'action' => $route->getActionName(),
            'middleware' => $this->getMiddleware($route),
        ]);
    }

    private function filterRoute(array $route)
    {
        if (count($route['middleware']->intersect($this->guard))==0) {
            return;
        }
        return $route;
    }

    private function getMiddleware($route)
    {
        return collect($route->gatherMiddleware())->map(function ($middleware) {
            return $middleware instanceof Closure ? 'Closure' : $middleware;
        });
    }
}