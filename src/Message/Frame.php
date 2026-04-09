<?php declare(strict_types=1);

namespace FrameFlow\Message;

use FrameFlow\Util\Uid;

/**
 *
 */
class Frame
{
    public const array RESERVED = [
        'id', 'payload-length', 'checksum', 'payload'
    ];

    public string $payload = '';

    public int $size {
        get => strlen($this->payload);
    }

    public string $checksum {
        get => hash($this->header->checksumHash, $this->payload);
    }

    public array $requires = [];

    /**
     * @param Header $header
     * @param string|Uid $id
     * @param string $type
     * @param Meta $meta
     */
    public function __construct(
        public readonly Header $header,
        public readonly string|Uid $id = new Uid(),
        public string $type = 'message',
        public Meta $meta = new Meta(),
    ) {}

    /**
     * @param string|Frame ...$requires
     * @return array
     */
    public function requires(string|Frame ...$requires): array
    {
        if (!empty($requires)) {
            foreach ($requires as $require) {
                $this->requires[] = ($require instanceof Frame) ? $require->id : $require;
            }
        }

        return $this->requires;
    }
}