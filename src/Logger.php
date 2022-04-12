<?php

namespace Robbinploeger\OopTesting;

class Logger {
    public function log(string $message): void
    {
        echo $message;
    }
}
