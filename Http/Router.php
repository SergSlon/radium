<?php
/*!
 * Radium
 * Copyright (C) 2011-2012 Jack P.
 * https://github.com/nirix
 *
 * This file is part of Radium.
 *
 * Radium is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation; version 3 only.
 *
 * Radium is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Radium. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Radium\Http;

use Radium\Core\Exception as Exception;

/**
 * Radium's Router.
 *
 * @since 0.1
 * @package Radium
 * @subpackage HTTP
 * @author Jack P.
 * @copyright (C) Jack P.
 */
class Router
{
    private static $routes = [];

    // Routed values
    public static $controller;
    public static $method;
    public static $params = [];

    /**
     * Adds a route to be routed.
     *
     * @param string $route  URI to match
     * @param string $value  Controller/method to route to
     * @param array  $params Default params to pass to the method
     */
    public static function add($route, $value, array $params = [])
    {
        // Don't overwrite the route
        if (!isset(static::$routes[$route])) {
            static::$routes[$route] = [
                'route'  => $route,
                'value'  => $value,
                'params' => $params
            ];
        }
    }

    /**
     * Routes the request to the controller.
     *
     * @param Request $request
     */
    public static function route(Request $request)
    {
        // Is this the root route?
        if ($request->uri() === '/' and isset(static::$routes['root'])) {
            return static::setRoute(static::$routes['root']);
        }

        // Do we have an exact match?
        if (isset(static::$routes[$request->uri()])) {
            return static::setRoute(static::$routes[$request->uri()]);
        }

        // The fun begins
        foreach (static::$routes as $route) {
            // Does the route match the request?
            if (preg_match("#^{$route['route']}?$#", $request->uri(), $params)) {
                unset($params[0]);
                $route['params'] = array_merge($route['params'], $params);
                $route['value'] = preg_replace("#^{$route['route']}?$#", $route['value'], $request->uri());
                return static::setRoute($route);
            }
        }

        // No matches, try 404 route
        if (isset(static::$routes['404'])) {
            return static::setRoute(static::$routes['404']);
        }
        // No 404 route, Exception time! FUN :D
        else {
            throw new Exception("No routes found for '{$request->uri()}'");
        }
    }

    /**
     * Sets the route info to that of the 404 route.
     */
    public static function set404()
    {
        if (!isset(static::$routes['404'])) {
            throw new Exception("There is no 404 route set.");
        }
        return static::setRoute(static::$routes['404']);
    }

    private static function setRoute($route)
    {
        $value = explode('.', $route['value']);
        $method = explode('/', $value[1]);

        static::$controller = str_replace('::', '\\', '\\'.$value[0]);
        static::$method = $method[0];
        static::$params = (isset($method[1]) and $params = explode(',', $method[1])) ? $params : [];
    }
}
