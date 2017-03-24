<?php
namespace Test;

use PHPUnit\Framework\TestCase;
use Sandbox\CachePool;

class Test extends TestCase
{
    function create()
    {
        return new CachePool(__DIR__ . '/../data/');
    }

    function test()
    {
        $pool = $this->create();

        $item = $pool->getItem('aaa');

        assert($item->isHit()  === false);
        assert($item->getKey() === 'aaa');
        assert($item->get()    === null);

        $item->set(123);

        assert($item->isHit()  === true);
        assert($item->get()    === 123);

        $item = $pool->getItem('aaa');

        assert($item->isHit()  === true);
        assert($item->get()    === 123);

        $item->expiresAfter(-1);

        assert($item->isHit()  === false);
        assert($item->get()    === null);

        $item = $pool->getItem('aaa');

        assert($item->isHit()  === false);
        assert($item->get()    === null);

        $item->set(123);

        assert($item->isHit()  === true);
        assert($item->get()    === 123);

        $pool->commit();

        $pool->deleteItem('aaa');

        assert($item->isHit()  === false);
        assert($item->get()    === null);

        $this->assertTrue(true);
    }

    function test_persistent()
    {
        $pool = $this->create();
        $item = $pool->getItem('xxx');
        $item->set(999);
        $pool->commit();

        $pool = $this->create();
        $item = $pool->getItem('xxx');
        assert($item->get() === 999);

        $this->assertTrue(true);
    }

    function test_persistent_expire()
    {
        $pool = $this->create();
        $item = $pool->getItem('xxx');
        $item->set(999);
        $pool->commit();

        // 有効期限切れにする
        $path = __DIR__ . '/../data/xxx';
        touch($path, 1);

        $pool = $this->create();
        $item = $pool->getItem('xxx');

        assert($item->isHit()  === false);
        assert($item->get()    === null);

        $this->assertTrue(true);
    }
}
