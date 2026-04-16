<?php declare(strict_types=1);

namespace FrameFlow\Util;

use DateTimeInterface;

/**
 *
 */
class Uid implements \Stringable
{
    const string TYPE_UID = 'uid';
    const string TYPE_UUID = 'uuid';

    /**
     * @param DateTimeInterface|null $dateTime
     * @return string
     * @throws \Random\RandomException
     */
    public static function v7(?DateTimeInterface $dateTime = null): string
    {
        if ($dateTime) {
            $ts = (int)$dateTime->format('Uv');
        } else {
            [$usec, $sec] = explode(' ', microtime());
            $ts = (int)$sec * 1000 + (int)($usec * 1000);
        }

        $timeBytes = pack('Nn', ($ts >> 16) & 0xFFFFFFFF, $ts & 0xFFFF);
        $random = random_bytes(10);

        $randA = unpack('n', substr($random, 0, 2))[1];
        $randA = ($randA & 0x0FFF) | 0x7000;

        $randB = substr($random, 2);
        $randB[0] = chr((ord($randB[0]) & 0x3F) | 0x80);

        $bin = $timeBytes . pack('n', $randA) . $randB;

        $hex = bin2hex($bin);

        return sprintf('%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }

    protected string $id;

    /**
     * @param string $type
     * @throws \Random\RandomException
     */
    public function __construct(string $type = self::TYPE_UID)
    {
        $this->id = match($type) {
            static::TYPE_UUID => static::v7(),
            default => uniqid(),
        };
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id;
    }
}