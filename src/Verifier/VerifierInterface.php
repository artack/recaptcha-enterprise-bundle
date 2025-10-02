<?php

namespace Artack\RecaptchaEnterpriseBundle\Service;

interface VerifierInterface
{
    public function verify(string $token, ?string $expectedAction = null): Result;
}
