<?php

namespace Artack\RecaptchaEnterpriseBundle\Service;

interface UserAgentResolverInterface
{
    public function resolveUserAgent(): ?string;
}
