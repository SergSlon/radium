<?php
/*!
 * FishHook
 * Copyright (C) 2009-2012 Jack P.
 * https://github.com/nirix
 *
 * FishHook is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation; version 3 only.
 *
 * FishHook is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with FishHook. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Radium\Core;

/**
 * The FishHook plugin library
 *
 * @package FishHook
 * @author Jack P.
 * @copyright (C) 2009-2012 Jack P.
 * @version 4.0
 */
class Hook
{
    private static $plugins = [];

    /**
     * Adds a plugin to the library
     *
     * @param string $class
     * @param mixed $plugin String of the function or array of the class and method.
     */
    public static function add($hook, $plugin)
    {
        // Make sure the hook index exists
        if (!isset(static::$plugins[$hook])) {
            static::$plugins[$hook] = [];
        }

        // Add the plugin
        static::$plugins[$hook][] = $plugin;
    }

    /**
     * Executes a hook
     *
     * @param string $hook
     * @param array $params Parameters to be passed to the plugins method.
     */
    public static function run($hook, $params = [])
    {
        // Make sure the hook index exists
        if (!isset(static::$plugins[$hook])) {
            return false;
        }

        // Make sure $params is an array
        if (!is_array($params)) {
            throw new Exception('Parameters to be passed to plugin methods need to be wrapped in an array.');
        }

        // Run the hook
        foreach (static::$plugins[$hook] as $plugin) {
            call_user_func_array($plugin, $params);
        }
    }
}
