<?php declare(strict_types=1);

namespace Yaspcc\Cache;

interface CacheServiceInterface
{
    function set(string $key, string $value, int $expire = null) : void;

    function get(string $key) : ?string;

    function exists(string $key): bool;

    /**
     * @param string[] $keys
     * @return string[]
     */
    function getMany(array $keys): array;

    function delete(array $keys): int;
}