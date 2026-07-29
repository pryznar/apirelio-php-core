<?php

declare(strict_types=1);

namespace Tracium\Core\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tracium\Core\Config\TransportConfig;
use Tracium\Core\Contracts\IngestionClient;
use Tracium\Core\Transport\HttpBatchTransport;

final class HttpBatchTransportTest extends TestCase
{
    public function test_it_retries_and_delivers_the_shared_batch_payload(): void
    {
        $client = new class implements IngestionClient {
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
            new TransportConfig('https://ingest.tracium.test/', 'trc_test'),
        );

        $transport->send([['event_id' => 'event-1']]);

        self::assertSame(2, $client->attempts);
        self::assertSame('https://ingest.tracium.test/ingest/v1/events/batch', $client->endpoint);
        self::assertSame('trc_test', $client->apiKey);
        self::assertSame([['event_id' => 'event-1']], $client->events);
    }
}
