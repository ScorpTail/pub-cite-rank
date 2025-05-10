<?php

namespace App\Traits;

trait CacheTrait
{
    protected static $cache = [];

    protected function cache(string $key, callable $callback)
    {
        if (!isset(static::$cache[$key])) {
            static::$cache[$key] = $callback();
        }

        return static::$cache[$key];
    }
}