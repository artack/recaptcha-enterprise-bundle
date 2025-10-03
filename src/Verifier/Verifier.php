<?php

declare(strict_types=1);

namespace Artack\RecaptchaEnterpriseBundle\Verifier;

use Artack\RecaptchaEnterpriseBundle\Service\IpResolverInterface;
use Artack\RecaptchaEnterpriseBundle\Service\UserAgentResolverInterface;
use Symfony\Component\HttpClient\HttpClient;

use function sprintf;

final readonly class Verifier implements VerifierInterface
{
    public function __construct(
        private string $projectId,
        private string $siteKey,
        private string $apiKey,
        private IpResolverInterface $ipResolver,
        private UserAgentResolverInterface $userAgentResolver,
    ) {}

    public function verify(string $token, ?string $expectedAction = null): Result
    {
        $url = sprintf(
            'https://recaptchaenterprise.googleapis.com/v1/projects/%s/assessments?key=%s',
            $this->projectId,
            $this->apiKey,
        );

        $event = [
            'token' => $token,
            'siteKey' => $this->siteKey,
            'expectedAction' => $expectedAction,
        ];

        if ($ip = $this->ipResolver->resolveIp()) {
            $event['userIpAddress'] = $ip;
        }

        if ($userAgent = $this->userAgentResolver->resolveUserAgent()) {
            $event['userAgent'] = $userAgent;
        }

        $response = HttpClient::create()->request('POST', $url, [
            'json' => [
                'event' => array_filter($event),
            ],
        ])->toArray(false);

        $valid = (bool) ($response['tokenProperties']['valid'] ?? false);
        $action = $response['tokenProperties']['action'] ?? null;
        $score = $response['riskAnalysis']['score'] ?? null;

        $success = $valid && (null === $expectedAction || $action === $expectedAction);

        return new Result($success, $valid, $action, $score, $response);
    }
}
