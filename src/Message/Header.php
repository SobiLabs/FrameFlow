<?php declare(strict_types=1);

namespace FrameFlow\Message;

use DateTime;
use FrameFlow\Util\Uid;

/**
 *
 */
class Header
{
    public const array RESERVED = [
        'trace-id', 'timestamp', 'charset', 'version', 'checksum'
    ];

    public readonly string $traceID;
    public readonly DateTime $timestamp;
    public array|Meta $meta;

    /**
     * @param array|Meta $meta
     * @param int $version
     * @param string $charset
     * @param false|string $checksumHash
     * @throws \Random\RandomException
     */
    public function __construct(
        array|Meta $meta = new Meta(),
        public readonly int $version = 1,
        public readonly string $charset = 'utf-8',
        public readonly false|string $checksumHash = 'sha256',
    )
    {
        $this->timestamp = DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)));
        $this->traceID = Uid::v7($this->timestamp);

        if (is_array($meta)) {
            $meta = new Meta($meta);
        }
        $this->meta = $meta;
    }
}