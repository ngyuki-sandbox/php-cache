<?php
namespace Test;

use PHPUnit\Framework\TestCase;
use Sandbox\CachePool;
use Sandbox\SimpleCache;

class SimpleCacheTest extends TestCase
{
    function create()
    {
        $pool = new CachePool(__DIR__ . '/../data/');
        return new SimpleCache($pool);
    }

    function test()
    {
        $cache = $this->create();

        $cache->delete('simple');

        assert($cache->has('simple') === false);
        assert($cache->get('simple') === null);

        $cache->set('simple', 123);

        assert($cache->has('simple') === true);
        assert($cache->get('simple') === 123);

        $cache->delete('simple');

        assert($cache->has('simple') === false);
        assert($cache->get('simple') === null);
        
        $this->assertTrue(true);
    }
}
