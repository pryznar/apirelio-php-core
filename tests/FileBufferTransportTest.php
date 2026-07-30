<?php

declare(strict_types=1);

namespace Apirelio\Core\Tests;

use Apirelio\Core\Config\BufferConfig;
use Apirelio\Core\Contracts\EventTransport;
use Apirelio\Core\Transport\FileBufferTransport;
use PHPUnit\Framework\TestCase;

final class FileBufferTransportTest extends TestCase
{
    public function test_it_flushes_a_complete_batch_and_preserves_order(): void
    {
        $path = sys_get_temp_dir().'/apirelio-core-'.bin2hex(random_bytes(6)).'.ndjson';
        $target = new class implements EventTransport
        {
            /** @var list<array<string, mixed>> */
            public array $events = [];

            public function send(array $events): void
            {
                $this->events = $events;
            }
        };
        $buffer = new FileBufferTransport($target, new BufferConfig($path, 2, 60));

        try {
            $buffer->send([['event_id' => 'one']]);
            self::assertSame([], $target->events);
            $buffer->send([['event_id' => 'two']]);

            self::assertSame([
                ['event_id' => 'one'],
                ['event_id' => 'two'],
            ], $target->events);
            self::assertSame('', file_get_contents($path));
        } finally {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }
}
