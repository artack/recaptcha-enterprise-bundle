<?php

namespace Artack\RecaptchaEnterpriseBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;

final readonly class UserAgentResolver implements UserAgentResolverInterface
{
    public function __construct(private RequestStack $requestStack) {}

    public function resolveUserAgent(): ?string
    {
        return $this->requestStack->getCurrentRequest()?->headers->get('User-Agent');
    }
}
