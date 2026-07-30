<?php

declare(strict_types=1);

namespace Apirelio\Core\Data;

readonly class ApirelioApplication
{
    public function __construct(
        public string $id,
        public ?string $name = null,
    ) {}
}
