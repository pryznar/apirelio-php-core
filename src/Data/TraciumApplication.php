<?php

declare(strict_types=1);

namespace Tracium\Core\Data;

readonly class TraciumApplication
{
    public function __construct(
        public string $id,
        public ?string $name = null,
    ) {}
}
