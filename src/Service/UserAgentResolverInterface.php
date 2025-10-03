<?php

declare(strict_types=1);

namespace Artack\RecaptchaEnterpriseBundle\Service;

interface UserAgentResolverInterface
{
    public function resolveUserAgent(): ?string;
}
