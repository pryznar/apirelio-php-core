<?php

declare(strict_types=1);

namespace Apirelio\Core\Data;

readonly class ApirelioCustomer
{
    public function __construct(
        public string $id,
        public ?string $name = null,
        public ?string $plan = null,
    ) {}
}
