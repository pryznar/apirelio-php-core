<?php

declare(strict_types=1);

namespace Tracium\Core\Config;

final readonly class TransportConfig
{
    public function __construct(
        public string $endpoint,
        public string $apiKey,
        public float $timeoutSeconds = 2.0,
        public float $connectTimeoutSeconds = 0.5,
    ) {}
}
