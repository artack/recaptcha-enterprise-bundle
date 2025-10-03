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
    public ?string $actionName = null;

    public function __construct(?float $minScore = null, ?string $actionName = null, ?string $message = null, ?array $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->minScore = $minScore;
        $this->actionName = $actionName;
        $this->message = $message ?? $this->message;
    }

    public function validatedBy(): string
    {
        return self::class.'Validator';
    }
}
