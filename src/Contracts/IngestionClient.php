<?php

declare(strict_types=1);

namespace Tracium\Core\Contracts;

interface IngestionClient
{
    /** @param list<array<string, mixed>> $events */
    public function postBatch(
        string $endpoint,
        string $apiKey,
        array $events,
        float $timeoutSeconds,
        float $connectTimeoutSeconds,
    ): void;
}
