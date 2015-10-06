# Memcached cache slam solution 

Emulate lock in Memcached
Solves the problem described [here](http://cmyker.blogspot.com/2015/10/memcached-lock.html).

Usage example

```php
$memcachedLock = new \Cmyker\MemcachedLock\Service;
$item = $memcachedLock->getItem($key, $expirationSec, $ttlSec);
$result = null;
if ($item->isExpired()) {
    $item->getLockAndUpdate(function() use (&$result, $cacheUpdateCallback) {
        //cache is populated/refreshed here
        $result = $cacheUpdateCallback();
        return $result;
    });
}
if ($result === null) {
    //try from cache
    if (($result = $item->get()) === false) {
        //rare situation, it could happen only if cache does not exists at all yet, and
        //process that populates cache did not made it yet, fallback to no cache
        $result = $cacheUpdateCallback();
    }
}
return $result;
```
