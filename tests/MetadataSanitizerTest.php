<?php

declare(strict_types=1);

namespace Tracium\Core\Tests;

use PHPUnit\Framework\TestCase;
use Tracium\Core\MetadataSanitizer;

final class MetadataSanitizerTest extends TestCase
{
    public function test_it_allows_only_explicit_safe_and_bounded_metadata(): void
    {
        $metadata = [
            'region' => 'eu-central',
            'ignored' => 'no',
            'api_token' => 'secret',
            'header.x-api-version' => 'v2',
            'header.authorization' => 'Bearer hidden',
            'exception' => 'RuntimeException',
        ];

        $safe = (new MetadataSanitizer())->sanitize($metadata, ['region', 'api_token']);

        self::assertSame([
            'region' => 'eu-central',
            'header.x-api-version' => 'v2',
            'exception' => 'RuntimeException',
        ], $safe);
    }

    public function test_it_enforces_the_ingestion_item_and_payload_limits(): void
    {
        $metadata = [];
        $allowed = [];
        for ($index = 0; $index < 30; $index++) {
            $key = 'field_'.$index;
            $metadata[$key] = str_repeat('x', 500);
            $allowed[] = $key;
        }

        $safe = (new MetadataSanitizer())->sanitize($metadata, $allowed);
        $encoded = json_encode($safe, JSON_THROW_ON_ERROR);

        self::assertLessThanOrEqual(20, count($safe));
        self::assertLessThanOrEqual(4096, strlen($encoded));
    }
}
