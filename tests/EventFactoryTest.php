<?php

declare(strict_types=1);

namespace Apirelio\Core\Tests;

use Apirelio\Core\Data\ApirelioApplication;
use Apirelio\Core\Data\ApirelioCustomer;
use Apirelio\Core\Data\EventContext;
use Apirelio\Core\EventFactory;
use PHPUnit\Framework\TestCase;

final class EventFactoryTest extends TestCase
{
    public function test_it_builds_the_shared_bounded_event_contract(): void
    {
        $event = (new EventFactory)->create(new EventContext(
            service: 'billing-api',
            environment: 'production',
            method: 'post',
            route: '/api/invoices/{invoice}',
            routeName: 'invoice.create',
            status: 700,
            durationMilliseconds: -4,
            requestBytes: 8_000_000_000,
            responseBytes: 120,
            customer: new ApirelioCustomer('customer_42', 'Acme', 'growth'),
            application: new ApirelioApplication('erp', 'ERP'),
            apiVersion: 'v2',
            sdk: 'symfony',
            sdkVersion: '0.1.0',
            release: '2026.07.29.1',
            errorCode: null,
            metadata: ['region' => 'eu'],
        ));

        self::assertNotSame('', $event['event_id']);
        self::assertSame('POST', $event['method']);
        self::assertSame(599, $event['status']);
        self::assertSame(0, $event['duration_ms']);
        self::assertSame(4_294_967_295, $event['request_bytes']);
        self::assertSame('customer_42', $event['customer_id']);
        self::assertSame('erp', $event['application_id']);
    }
}
