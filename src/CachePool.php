<?php
namespace Sandbox;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class CachePool implements CacheItemPoolInterface
{
    /**
     * @var FileBackend
     */
    private $backend;

    /**
     * @var array|CacheItem[]
     */
    private $items = [];

    public function __construct($directory)
    {
        $this->backend = new FileBackend($directory);
    }

    public function __destruct()
    {
        $this->commit();
    }

    private function validateKey($key)
    {
        if (strlen($key) > 64) {
            throw new InvalidArgumentException("[$key] is invalid");
        }
        if (preg_match('/\A[._a-zA-Z0-9]+\z/', $key) == 0) {
            throw new InvalidArgumentException("[$key] is invalid");
        }
    }

    public function getItem($key)
    {
        $this->validateKey($key);

        if (isset($this->items[$key]) === false) {
            $data = null;
            if ($this->backend->has($key)) {
                $data = $this->backend->get($key);
            }
            $this->items[$key] = new CacheItem($key, $data);
        }

        return $this->items[$key];
    }

    public function getItems(array $keys = array())
    {
        return array_map(function ($key) {
            return $this->getItem($key);
        }, $keys);
    }

    public function hasItem($key)
    {
        return $this->getItem($key)->isHit();
    }

    public function clear()
    {
        $this->items = [];
        return $this->backend->clear();
    }

    public function deleteItem($key)
    {
        $this->validateKey($key);

        if (isset($this->items[$key])) {
            $this->items[$key]->set(null);
        }

        unset($this->items[$key]);
        $this->backend->delete($key);

        return true;
    }

    public function deleteItems(array $keys)
    {
        return array_reduce($keys, function ($r, $key) {
            return $r || $this->deleteItem($key);
        }, false);
    }

    public function save(CacheItemInterface $item)
    {
        $key = $item->getKey();

        if (isset($this->items[$key]) === false) {
            return false;
        }

        $item = $this->items[$key];
        $item->dirty(false);

        if ($item->isHit()) {
            return $this->backend->save($key, $item->get(), $item->getExpirationTime());
        } else {
            return $this->backend->delete($key);
        }
    }

    public function saveDeferred(CacheItemInterface $item)
    {
        $key = $item->getKey();
        if (isset($this->items[$key])) {
            $this->items[$key]->dirty(true);
        }
        return true;
    }

    public function commit()
    {
        foreach ($this->items as $item) {
            if ($item->isDirty()) {
                $this->save($item);
            }
        }
        return true;
    }
}
