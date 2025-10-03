<?php

declare(strict_types=1);

namespace Artack\RecaptchaEnterpriseBundle\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
final class RecaptchaEnterprise extends Constraint
{
    public string $message = 'You may be sending automated requests.';

    public ?float $minScore = null;
    public ?string $action = null;

    public function __construct(?float $minScore = null, ?string $action = null, ?string $message = null, ?array $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->minScore = $minScore;
        $this->action = $action;
        $this->message = $message ?? $this->message;
    }

    public function validatedBy(): string
    {
        return self::class.'Validator';
    }
}
