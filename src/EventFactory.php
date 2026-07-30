<?php

declare(strict_types=1);

namespace Apirelio\Core;

use Apirelio\Core\Data\EventContext;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Component\Uid\Ulid;

final readonly class EventFactory
{
    /** @return array<string, mixed> */
    public function create(EventContext $context): array
    {
        return [
            'event_id' => (string) new Ulid,
            'occurred_at' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d\TH:i:s.v\Z'),
            'service' => $context->service,
            'environment' => $context->environment,
            'method' => strtoupper($context->method),
            'route' => $context->route,
            'route_name' => $context->routeName,
            'status' => min(599, max(100, $context->status)),
            'duration_ms' => $this->unsignedInteger($context->durationMilliseconds),
            'request_bytes' => $this->unsignedInteger($context->requestBytes),
            'response_bytes' => $this->unsignedInteger($context->responseBytes),
            'customer_id' => $context->customer?->id,
            'customer_name' => $context->customer?->name,
            'customer_plan' => $context->customer?->plan,
            'application_id' => $context->application?->id,
            'application_name' => $context->application?->name,
            'api_version' => $context->apiVersion,
            'sdk' => $context->sdk,
            'sdk_version' => $context->sdkVersion,
            'release' => $context->release,
            'error_code' => $context->errorCode,
            'metadata' => $context->metadata,
        ];
    }

    private function unsignedInteger(int $value): int
    {
        return min(4_294_967_295, max(0, $value));
    }
}
