<?php

namespace App\Traits;

use App\Enums\CacheGroupEnum;
use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * Groups cache entries so a whole group can be invalidated at once without
 * relying on cache tags, which the default `database` store does not support.
 *
 * Every key carries the current version of its group. Flushing a group bumps
 * that version, which orphans the previous keys and lets them fall off on
 * their own TTL.
 */
trait WithVersionedCache
{
    /**
     * Current version number of a cache group.
     */
    protected function cacheGroupVersion(CacheGroupEnum $group): int
    {
        return (int) Cache::rememberForever($this->cacheVersionKey($group), fn () => 1);
    }

    /**
     * Build a cache key scoped to the current version of its group.
     */
    protected function versionedCacheKey(CacheGroupEnum $group, string $key): string
    {
        return "{$group->value}:{$key}:v{$this->cacheGroupVersion($group)}";
    }

    /**
     * Remember a value within a cache group, serving it stale while it refreshes.
     *
     * @param  array{0: int, 1: int}  $duration  Seconds the value stays fresh, then how long it may be served stale.
     */
    protected function flexibleVersioned(CacheGroupEnum $group, string $key, array $duration, Closure $callback): mixed
    {
        return Cache::flexible($this->versionedCacheKey($group, $key), $duration, $callback);
    }

    /**
     * Invalidate every cache entry belonging to the given groups.
     */
    protected function flushCacheGroup(CacheGroupEnum ...$groups): void
    {
        foreach ($groups as $group) {
            $versionKey = $this->cacheVersionKey($group);

            if (Cache::increment($versionKey) === false) {
                Cache::forever($versionKey, 1);
            }
        }
    }

    private function cacheVersionKey(CacheGroupEnum $group): string
    {
        return "cache:version:{$group->value}";
    }
}
