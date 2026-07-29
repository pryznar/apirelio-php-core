<?php

declare(strict_types=1);

namespace Tracium\Core\Tests;

use PHPUnit\Framework\TestCase;
use Tracium\Core\ErrorCodeExtractor;

final class ErrorCodeExtractorTest extends TestCase
{
    public function test_explicit_code_wins_over_the_response(): void
    {
        $extractor = new ErrorCodeExtractor();

        self::assertSame(
            'EXPLICIT',
            $extractor->extract('EXPLICIT', '{"error":{"code":"RESPONSE"}}'),
        );
        self::assertSame(
            'NESTED',
            $extractor->extract(null, '{"problem":{"id":"NESTED"}}', 'problem.id'),
        );
        self::assertNull($extractor->extract(null, 'not-json'));
    }
}
