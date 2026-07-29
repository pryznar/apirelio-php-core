<?php

declare(strict_types=1);

namespace Tracium\Core\Transport;

use RuntimeException;
use Throwable;
use Tracium\Core\Config\TransportConfig;
use Tracium\Core\Contracts\EventTransport;
use Tracium\Core\Contracts\IngestionClient;

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
            throw new RuntimeException('TRACIUM_API_KEY is not configured.');
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

        throw new RuntimeException('Unable to deliver Tracium events.', 0, $lastException);
    }
}
