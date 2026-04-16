<?php declare(strict_types=1);

namespace FrameFlow;

use FrameFlow\Message\Frame;
use FrameFlow\Message\Header;
use FrameFlow\Util\Uid;

/**
 *
 */
class Message
{
    public protected(set) array $frames = [];

    /**
     * @param string|Frame ...$frames
     * @return self
     */
    public static function make(string|Frame ...$frames): self
    {
        return new self(new Header(), ...$frames);
    }

    /**
     * @param Header $header
     * @param string|Frame ...$frames
     */
    public function __construct(
        public readonly Header $header = new Header(),
        string|Frame ...$frames
    )
    {
        $this->add(...$frames);
    }

    /**
     * @param string|Frame ...$frames
     * @return void
     */
    public function add(string|Frame ...$frames): void
    {
        foreach ($frames as $frame) {
            if (is_string($frame)) {
                $payload = $frame;
                $frame = new Frame($this->header);
                $frame->payload = $payload;
            }

            $this->frames[(string)$frame->id] = $frame;
        }
    }

    /**
     * @param string|Uid $id
     * @return Frame|null
     */
    public function get(string|Uid $id): ?Frame
    {
        return $this->frames[(string)$id] ?? null;
    }

    /**
     * @param string|Uid $id
     * @return bool
     */
    public function has(string|Uid $id): bool
    {
        return isset($this->frames[(string)$id]);
    }

    /**
     * @param string|Frame $frame
     * @return void
     */
    public function remove(string|Frame $frame): void
    {
        if ($frame instanceof Frame) {
            $frame = (string)$frame->id;
        }

        if (!$this->has($frame)) return;

        unset($this->frames[$frame]);
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->frames = [];
    }
}