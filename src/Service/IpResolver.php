<?php

declare(strict_types=1);

namespace Artack\RecaptchaEnterpriseBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;

final readonly class IpResolver implements IpResolverInterface
{
    public function __construct(private RequestStack $requestStack) {}

    public function resolveIp(): ?string
    {
        return $this->requestStack->getCurrentRequest()?->getClientIp();
    }
}
