<?php

declare(strict_types=1);

namespace Artack\RecaptchaEnterpriseBundle\Service;

interface IpResolverInterface
{
    public function resolveIp(): ?string;
}
