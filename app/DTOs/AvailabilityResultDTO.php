<?php

namespace App\DTOs;

final readonly class AvailabilityResultDTO
{
    public function __construct(
        public bool $isAvailable,
        public string $message,
        public ?string $reason = null
    ) {}
}
