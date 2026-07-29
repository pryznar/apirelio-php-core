<?php

declare(strict_types=1);

namespace Tracium\Core;

final readonly class MetadataSanitizer
{
    private const MAX_ITEMS = 20;
    private const MAX_BYTES = 4096;

    /** @var list<string> */
    private const SENSITIVE_KEYS = [
        'authorization',
        'cookie',
        'password',
        'token',
        'secret',
        'email',
        'ip',
    ];

    /**
     * @param array<string, bool|float|int|string|null> $metadata
     * @param list<string> $allowedKeys
     * @return array<string, bool|float|int|string|null>
     */
    public function sanitize(array $metadata, array $allowedKeys): array
    {
        $safe = [];

        foreach ($metadata as $key => $value) {
            if ($this->isSensitive($key) || ! $this->isAllowed($key, $allowedKeys)) {
                continue;
            }

            $candidate = $safe;
            $candidate[mb_substr($key, 0, 120)] = is_string($value)
                ? mb_substr($value, 0, 1000)
                : $value;
            $encoded = json_encode($candidate);
            if ($encoded === false || strlen($encoded) > self::MAX_BYTES) {
                continue;
            }

            $safe = $candidate;
            if (count($safe) === self::MAX_ITEMS) {
                break;
            }
        }

        return $safe;
    }

    /** @param list<string> $allowedKeys */
    private function isAllowed(string $key, array $allowedKeys): bool
    {
        return str_starts_with($key, 'header.')
            || $key === 'exception'
            || in_array($key, $allowedKeys, true);
    }

    private function isSensitive(string $key): bool
    {
        $normalized = strtolower($key);
        foreach (self::SENSITIVE_KEYS as $sensitive) {
            if (str_contains($normalized, $sensitive)) {
                return true;
            }
        }

        return false;
    }
}
