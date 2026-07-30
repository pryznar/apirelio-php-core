<?php

declare(strict_types=1);

namespace Apirelio\Core\Transport;

use Apirelio\Core\Config\TransportConfig;
use Apirelio\Core\Contracts\EventTransport;
use Apirelio\Core\Contracts\IngestionClient;
use RuntimeException;
use Throwable;

class HttpBatchTransport implements EventTransport
{
    public function __construct(
        private readonly IngestionClient $client,
        private readonly TransportConfig $config,
    ) {}

    public function send(array $events): void
    {
        if ($events === []) {
            return;
        }
        if ($this->config->apiKey === '') {
            throw new RuntimeException('APIRELIO_API_KEY is not configured.');
        }

        $lastException = null;
        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $this->client->postBatch(
                    rtrim($this->config->endpoint, '/').'/ingest/v1/events/batch',
                    $this->config->apiKey,
                    $events,
                    $this->config->timeoutSeconds,
                    $this->config->connectTimeoutSeconds,
                );

                return;
            } catch (Throwable $exception) {
                $lastException = $exception;
                if ($attempt < 2) {
                    usleep(100_000 * ($attempt + 1));
                }
            }
        }

        throw new RuntimeException('Unable to deliver Apirelio events.', 0, $lastException);
    }
}
