<?php

declare(strict_types=1);

namespace Artack\RecaptchaEnterpriseBundle\Service;

final readonly class Result
{
    /**
     * @param array<mixed> $raw
     */
    public function __construct(
        public bool $success,
        public bool $valid,
        public ?string $action,
        public ?float $score,
        public array $raw,
    ) {}
}
