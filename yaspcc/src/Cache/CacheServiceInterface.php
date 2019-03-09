<?php declare(strict_types=1);

namespace Yaspcc\Cache;

interface CacheServiceInterface
{
    function set(string $key, $value, $expire = null);

    function get(string $key);

    function exists(string $key): bool;
}