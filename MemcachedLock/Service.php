<?php

namespace Cmyker\MemcachedLock;

/**
 * Class to cache data in memcached
 * The main idea that cached data is not deleted on expiration, but you can get check whether it is already expired
 * by calling isExpired() method. Then you obtain lock to deal with a situiation when multiple proceesses are trying
 * to refresh the cache.
 * Updating cache data is atomic operation and as a result it allows you to have the data in cache always.
 */
class Service
{

    /**
     * Inject \Memcached intanse depedency here
     *
     * @var \Memcached
     */
    protected $memcached;

    public function __construct(\Memcached $memcached)
    {
        $this->memcached = $memcached;
    }

    /**
     * Get new Item object
     * after $expirationSec cache is considered to be expired and will should be refreshed
     * if after $ttlSec cache was not refreshed it will be comletely removed
     *
     * @param string $key data key
     * @param int $expirationSec after this amount of seconds cache is considered as expired and should be refreshed
     * @param int $ttlSec after this amount of seconds data will be deleted completely from memcached (ideally
     *                    this should never happen)
     *
     * @return Item
     */
    public function getItem($key, $expirationSec, $ttlSec)
    {
        return new Item($this->memcached, $key, $expirationSec, $ttlSec);
    }
}
