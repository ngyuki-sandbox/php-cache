<?php
namespace Sandbox;

use Psr\Cache\CacheItemInterface;

class CacheItem implements CacheItemInterface
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var int
     */
    private $expiration;

    /**
     * @var bool
     */
    private $dirty = false;

    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
        $this->expiration = $this->getDefaultExpiration();
    }

    private function getDefaultExpiration()
    {
        return time() + 60 * 60 * 24;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function get()
    {
        if ($this->isHit() === false) {
            return null;
        }
        return $this->value;
    }

    public function isHit()
    {
        if ($this->value === null) {
            return false;
        }
        if ($this->expiration < time()) {
            $this->value = null;
            $this->expiration = $this->getDefaultExpiration();
            $this->dirty();
            return false;
        }
        return true;
    }

    public function set($value)
    {
        $this->value = $value;
        $this->expiration = $this->getDefaultExpiration();

        return $this->dirty();
    }

    /**
     * @param \DateTimeInterface|null $expiration
     * @return $this|CacheItem
     */
    public function expiresAt($expiration)
    {
        if ($expiration === null) {
            $this->expiration = $this->getDefaultExpiration();
        } else {
            $this->expiration = $expiration->getTimestamp();
        }

        return $this->dirty();
    }

    /**
     * @param \DateInterval|int|null $time
     * @return $this
     */
    public function expiresAfter($time)
    {
        if ($time === null) {
            $this->expiration = $this->getDefaultExpiration();
        } else if (is_int($time)) {
            $this->expiration = time () + $time;
        } else {
            $this->expiration = (new \DateTimeImmutable())->add($time)->getTimestamp();
        }

        return $this->dirty();
    }

    /**
     * @return $this
     */
    public function dirty($dirty = true)
    {
        $this->dirty = $dirty;
        return $this;
    }

    public function isDirty()
    {
        return $this->dirty;
    }

    public function getExpirationTime()
    {
        return $this->expiration;
    }
}
