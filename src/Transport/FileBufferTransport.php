<?php

declare(strict_types=1);

namespace Tracium\Core\Transport;

use JsonException;
use RuntimeException;
use Tracium\Core\Config\BufferConfig;
use Tracium\Core\Contracts\EventTransport;

class FileBufferTransport implements EventTransport
{
    public function __construct(
        private readonly EventTransport $transport,
        private readonly BufferConfig $config,
    ) {}

    /** @throws JsonException */
    public function send(array $events): void
    {
        if ($events === []) {
            return;
        }

        $directory = dirname($this->config->path);
        if (! is_dir($directory) && ! mkdir($directory, 0775, true) && ! is_dir($directory)) {
            throw new RuntimeException('Unable to create the Tracium buffer directory.');
        }

        $lines = array_map(
            static fn (array $event): string => json_encode($event, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
            $events,
        );

        if (file_put_contents($this->config->path, implode("\n", $lines)."\n", FILE_APPEND | LOCK_EX) === false) {
            throw new RuntimeException('Unable to write the Tracium event buffer.');
        }

        $this->flushIfDue();
    }

    public function flushIfDue(bool $force = false): void
    {
        if (! is_file($this->config->path)) {
            return;
        }

        $size = max(1, $this->config->batchSize);
        $interval = max(1, $this->config->flushIntervalSeconds);
        $modifiedAt = filemtime($this->config->path) ?: time();

        if (! $force && $this->lineCount() < $size && (time() - $modifiedAt) < $interval) {
            return;
        }

        $this->flush($size);
    }

    private function flush(int $size): void
    {
        $handle = fopen($this->config->path, 'c+');
        if ($handle === false || ! flock($handle, LOCK_EX)) {
            throw new RuntimeException('Unable to lock the Tracium event buffer.');
        }

        try {
            rewind($handle);
            $lines = [];
            while (($line = fgets($handle)) !== false) {
                if (trim($line) !== '') {
                    $lines[] = $line;
                }
            }

            $batchLines = array_slice($lines, 0, $size);
            if ($batchLines === []) {
                return;
            }

            $events = array_map(
                static fn (string $line): array => (array) json_decode($line, true, 512, JSON_THROW_ON_ERROR),
                $batchLines,
            );
            $this->transport->send($events);

            $remaining = array_slice($lines, count($batchLines));
            ftruncate($handle, 0);
            rewind($handle);
            if ($remaining !== []) {
                fwrite($handle, implode('', $remaining));
            }
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    private function lineCount(): int
    {
        $handle = fopen($this->config->path, 'rb');
        if ($handle === false) {
            return 0;
        }

        $count = 0;
        while (fgets($handle) !== false) {
            $count++;
        }
        fclose($handle);

        return $count;
    }
}
