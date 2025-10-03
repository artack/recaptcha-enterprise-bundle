<?php

declare(strict_types=1);

namespace Artack\RecaptchaEnterpriseBundle\Verifier;

interface VerifierInterface
{
    public function verify(string $token, ?string $expectedAction = null): Result;

    public function getLatestResult(): ?Result;
}
