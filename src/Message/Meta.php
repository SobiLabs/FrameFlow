<?php declare(strict_types=1);

namespace FrameFlow\Message;

use Traversable;

/**
 *
 */
class Meta implements \IteratorAggregate
{
    /**
     * @param array $data
     */
    public function __construct(
        protected array $data = []
    ) {}

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function add(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (!$this->has($key)) return $default;

        return $this->data[$key];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * @param string $key
     * @return void
     */
    public function remove(string $key): void
    {
        if (!$this->has($key)) return;
        unset($this->data[$key]);
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->data = [];
    }

    /**
     * @return bool
     */
    public function empty(): bool
    {
        return empty($this->data);
    }

    /**
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->data);
    }
}