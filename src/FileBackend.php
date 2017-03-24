<?php
namespace Sandbox;

class FileBackend
{
    /**
     * @var string
     */
    private $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    private function path($key)
    {
        return $this->directory . DIRECTORY_SEPARATOR . $key;
    }

    public function has($key)
    {
        clearstatcache();

        $path = $this->path($key);
        if (file_exists($path) === false) {
            return false;
        }

        $time = filemtime($path);
        if ($time < time()) {
            return false;
        }

        return true;
    }

    public function get($key)
    {
        if ($this->has($key) === false) {
            return false;
        }

        $path = $this->path($key);
        $data = file_get_contents($path);
        if ($data === false) {
            return false;
        }

        $data = unserialize($data);
        return $data;
    }

    public function clear()
    {
        // @todo
        return true;
    }

    public function delete($key)
    {
        clearstatcache();

        $path = $this->path($key);
        if (file_exists($path) === false) {
            return false;
        }

        return unlink($path);
    }

    public function save($key, $data, $time)
    {
        $path = $this->path($key);
        $data = serialize($data);

        return file_put_contents($path, $data) && touch($path, $time);
    }
}
