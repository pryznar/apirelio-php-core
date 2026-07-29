<?php

declare(strict_types=1);

namespace Tracium\Core\Config;

final readonly class BufferConfig
{
    public function __construct(
        public string $path,
        public int $batchSize = 500,
        public int $flushIntervalSeconds = 10,
    ) {}
}
