<?php

namespace Artack\RecaptchaEnterpriseBundle\Service;

final readonly class Result
{
    public function __construct(
        public bool $success,
        public bool $valid,
        public ?string $action,
        public ?float $score,
        public array $raw,
    ) {}
}
