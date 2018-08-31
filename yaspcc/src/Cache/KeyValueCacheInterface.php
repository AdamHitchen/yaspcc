<?php declare(strict_types=1);

namespace Yaspcc\Cache;

interface KeyValueCacheInterface
{
    function set(string $key, string $value, $expire = null);

    function get(string $key);

    function exists(string $key): bool;
}