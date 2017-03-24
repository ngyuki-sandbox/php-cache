<?php
namespace Sandbox;

use Psr\SimpleCache\CacheInterface;

class SimpleCache implements CacheInterface
{
    /**
     * @var CachePool
     */
    private $pool;

    public function __construct(CachePool $pool)
    {
        $this->pool = $pool;
    }

    public function get($key, $default = null)
    {
        return $this->pool->getItem($key)->get() ?? $default;
    }

    public function set($key, $value, $ttl = null)
    {
        $item = $this->pool->getItem($key)->set($value)->expiresAfter($ttl);
        return $this->pool->save($item);
    }

    public function delete($key)
    {
        return $this->pool->deleteItem($key);
    }

    public function getMultiple($keys, $default = null)
    {
        return array_map(function ($key) use ($default) {
            return $this->get($key, $default);
        }, $keys);
    }

    public function setMultiple($values, $ttl = null)
    {
        $result = true;
        foreach ($values as $key => $value) {
            if ($this->set($key, $values, $ttl) === false) {
                $result = false;
            }
        }
        return $result;
    }

    public function deleteMultiple($keys)
    {
        $result = true;
        foreach ($keys as $key) {
            if ($this->delete($key) === false) {
                $result = false;
            }
        }
        return $result;
    }

    public function has($key)
    {
        return $this->pool->hasItem($key);
    }

    public function clear()
    {
        return $this->pool->clear();
    }
}
