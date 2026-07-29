<?php

declare(strict_types=1);

namespace Tracium\Core\Data;

final readonly class EventContext
{
    /**
     * @param array<string, bool|float|int|string|null> $metadata
     */
    public function __construct(
        public string $service,
        public string $environment,
        public string $method,
        public string $route,
        public ?string $routeName,
        public int $status,
        public int $durationMilliseconds,
        public int $requestBytes,
        public int $responseBytes,
        public ?TraciumCustomer $customer,
        public ?TraciumApplication $application,
        public ?string $apiVersion,
        public string $sdk,
        public string $sdkVersion,
        public ?string $release,
        public ?string $errorCode,
        public array $metadata,
    ) {}
}
