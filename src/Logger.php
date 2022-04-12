<?php

namespace Robbinploeger\OopTesting;

class Logger
{
    public const INFO = 'INFO';

    public bool $enabled;

    public function info(string $message): void
    {
        $this->log(self::INFO, $message);
    }

    public function log(string $level, string $message): void
    {
        echo "$level: $message";
    }
}
