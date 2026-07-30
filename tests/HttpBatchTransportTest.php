<?php

declare(strict_types=1);

namespace Apirelio\Core\Tests;

use Apirelio\Core\Config\TransportConfig;
use Apirelio\Core\Contracts\IngestionClient;
use Apirelio\Core\Transport\HttpBatchTransport;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class HttpBatchTransportTest extends TestCase
{
    public function test_it_retries_and_delivers_the_shared_batch_payload(): void
    {
        $client = new class implements IngestionClient
        {
            public int $attempts = 0;

            /** @var list<array<string, mixed>> */
            public array $events = [];

            public string $endpoint = '';

            public string $apiKey = '';

            public function postBatch(
                string $endpoint,
                string $apiKey,
                array $events,
                float $timeoutSeconds,
                float $connectTimeoutSeconds,
            ): void {
                $this->attempts++;
                if ($this->attempts === 1) {
                    throw new RuntimeException('Temporary failure');
                }

                $this->endpoint = $endpoint;
                $this->apiKey = $apiKey;
                $this->events = $events;
            }
        };
        $transport = new HttpBatchTransport(
            $client,
            new TransportConfig('https://ingest.apirelio.test/', 'apr_test'),
        );

        $transport->send([['event_id' => 'event-1']]);

        self::assertSame(2, $client->attempts);
        self::assertSame('https://ingest.apirelio.test/ingest/v1/events/batch', $client->endpoint);
        self::assertSame('apr_test', $client->apiKey);
        self::assertSame([['event_id' => 'event-1']], $client->events);
    }
}
