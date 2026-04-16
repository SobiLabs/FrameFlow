<?php declare(strict_types=1);

namespace FrameFlow;

use FrameFlow\Message\Frame;
use FrameFlow\Message\Header;

class Encoder
{
    public const string NAME = 'FrameFlow';
    public const string VERSION = '1.0';
    public const string DELIMITER = '---';

    /**
     * @param Message $message
     * @return string
     */
    public static function encode(Message $message): string
    {
        $str = static::DELIMITER.' '.static::NAME.'-'.static::VERSION.' '.static::DELIMITER."\n";
        $str .= static::header($message->header);
        $str .= static::body($message->header, ...$message->frames);

        return $str;
    }

    /**
     * @param Header $header
     * @return string
     */
    protected static function header(Header $header): string
    {
        $str = <<<HEADER
        Trace-Id: $header->traceID
        Timestamp: {$header->timestamp->format(DATE_ATOM)}
        Charset: $header->charset
        Version: $header->version
        HEADER;

        if ($header->checksumHash !== false) {
            $str .= "\nChecksum: $header->checksumHash";
        }

        foreach ($header->meta as $key => $value) {
            if (in_array(strtolower($key), Header::RESERVED)) continue;

            $value = str_replace(["\r\n", "\r", "\n"], '\n', trim($value));
            $str .= "\n$key: $value";
        }

        return $str."\n".static::DELIMITER."\n";
    }

    /**
     * @param Header $header
     * @param Frame ...$frames
     * @return string
     */
    protected static function body(Header $header, Frame ...$frames): string
    {
        $str = '';

        foreach ($frames as $frame) {
            $str .= "\n".static::DELIMITER.' Frame '.static::DELIMITER."\n";
            $str .= "Id: $frame->id\n";

            foreach ($frame->meta as $key => $value) {
                if (in_array(strtolower($key), Frame::RESERVED)) continue;

                $value = str_replace(["\r\n", "\r", "\n"], '\n', trim($value));
                $str .= "$key: $value\n";
            }

            $str .= "Payload-Length: $frame->size\n";

            if (!empty($frame->payload)) {
                if ($header->checksumHash !== false) {
                    $str .= "Checksum: $frame->checksum\n";
                }

                $str .= "Payload:\n";
                $str .= $frame->payload."\n".static::DELIMITER."\n";
            }
        }

        return $str;
    }
}