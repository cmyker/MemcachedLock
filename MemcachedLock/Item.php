<?php

namespace Cmyker\MemcachedLock;

class Item
{

    /**
     * @var Memcached
     */
    protected $memcached;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $expirationKey;

    /**
     * @var int
     */
    protected $expirationSec;

    /**
     * @var int
     */
    protected $ttlSec;

    /**
     * @param \Memcached $memcached
     * @param string $key
     * @param int $expirationSec
     * @param int $ttlSec
     */
    public function __construct(\Memcached $memcached, $key, $expirationSec, $ttlSec)
    {
        $this->memcached = $memcached;
        $this->key = $this->getKeyHash($key);
        $this->expirationKey = $this->getKeyExpirationHash($key);
        $this->expirationSec = $expirationSec;
        $this->ttlSec = $ttlSec;
    }

    /**
     * Get cache data
     *
     * @return mixed|false
     */
    public function get()
    {
        return $this->memcached->get($this->key);
    }

    /**
     * Obtain a NON-BLOCKING lock and update cache data
     *
     * @param callable $callback callback return value is used to update the cache
     */
    public function getLockAndUpdate(callable $callback)
    {
        if ($this->memcached->add($this->expirationKey, true, $this->expirationSec)) {
            //key was created here
            $this->update(call_user_func($callback));
        }
        //and here it is already exists, do nothing
    }

    protected function update($value)
    {
        $this->memcached->set($this->key, $value, $this->ttlSec);
    }

    /**
     * Check whether cache is expired
     *
     * @return bool
     */
    public function isExpired()
    {
        if ($this->memcached->get($this->expirationKey) === false) {
            return true;
        }
    }

    /**
     * @param $key
     * @return string
     */
    protected function getKeyHash($key)
    {
        return md5($key);
    }

    /**
     * @param $key
     * @return string
     */
    protected function getKeyExpirationHash($key)
    {
        return $this->getKeyHash('exp'.$key);
    }

}
