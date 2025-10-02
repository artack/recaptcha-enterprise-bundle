<?php

namespace Artack\RecaptchaEnterpriseBundle\Service;

interface IpResolverInterface
{
    public function resolveIp(): ?string;
}
