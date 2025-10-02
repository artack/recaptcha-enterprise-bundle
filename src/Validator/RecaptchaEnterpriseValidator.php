<?php

namespace Artack\RecaptchaEnterpriseBundle\Validator;

use Artack\RecaptchaEnterpriseBundle\Service\VerifierInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class RecaptchaEnterpriseValidator extends ConstraintValidator
{
    public function __construct(
        private readonly VerifierInterface $verifier,
        private readonly float $minScore,
    ) {}

    /**
     * @param RecaptchaEnterprise $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof RecaptchaEnterprise) {
            throw new UnexpectedTypeException($constraint, RecaptchaEnterprise::class);
        }

        if (null === $value || '' === $value) {
            throw new UnexpectedValueException($value, 'string');
        }

        $result = $this->verifier->verify($value, $constraint->action);

        $minScore = $constraint->minScore ?? $this->minScore;

        if (!$result->success || ($result->score && $result->score < $minScore)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
