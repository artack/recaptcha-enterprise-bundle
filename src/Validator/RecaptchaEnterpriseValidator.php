<?php

declare(strict_types=1);

namespace Artack\RecaptchaEnterpriseBundle\Validator;

use Artack\RecaptchaEnterpriseBundle\Verifier\VerifierInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

use function is_string;

final class RecaptchaEnterpriseValidator extends ConstraintValidator
{
    public function __construct(
        private readonly VerifierInterface $verifier,
        private readonly bool $enabled,
        private readonly float $minScore,
    ) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof RecaptchaEnterprise) {
            throw new UnexpectedTypeException($constraint, RecaptchaEnterprise::class);
        }

        if (!$this->enabled) {
            return;
        }

        if (null === $value) {
            $value = '';
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $result = $this->verifier->verify($value, $constraint->actionName);

        $minScore = $constraint->minScore ?? $this->minScore;

        if (!$result->success || (null !== $result->score && $result->score < $minScore)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
