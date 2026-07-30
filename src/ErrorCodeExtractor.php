<?php

declare(strict_types=1);

namespace Apirelio\Core;

final readonly class ErrorCodeExtractor
{
    public function extract(?string $explicit, ?string $json, string $path = 'error.code'): ?string
    {
        if ($explicit !== null && $explicit !== '') {
            return mb_substr($explicit, 0, 255);
        }
        if ($json === null || $json === '') {
            return null;
        }

        $decoded = json_decode($json, true);
        $value = is_array($decoded) ? $this->valueAtPath($decoded, $path) : null;

        return is_scalar($value) ? mb_substr((string) $value, 0, 255) : null;
    }

    /** @param array<string, mixed> $data */
    private function valueAtPath(array $data, string $path): mixed
    {
        $value = $data;
        foreach (explode('.', $path) as $segment) {
            if (! is_array($value) || ! array_key_exists($segment, $value)) {
                return null;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}
