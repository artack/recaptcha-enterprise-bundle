<?php

declare(strict_types=1);

namespace Artack\RecaptchaEnterpriseBundle;

use Artack\RecaptchaEnterpriseBundle\Form\RecaptchaEnterpriseType;
use Artack\RecaptchaEnterpriseBundle\Service\IpResolver;
use Artack\RecaptchaEnterpriseBundle\Service\UserAgentResolver;
use Artack\RecaptchaEnterpriseBundle\Validator\RecaptchaEnterpriseValidator;
use Artack\RecaptchaEnterpriseBundle\Verifier\Verifier;
use Artack\RecaptchaEnterpriseBundle\Verifier\VerifierInterface;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class RecaptchaEnterpriseBundle extends AbstractBundle
{
    protected string $extensionAlias = 'artack_recaptcha_enterprise';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
            ->booleanNode('enabled')->defaultValue(true)->end()
            ->scalarNode('site_key')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('project_id')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('api_key')->isRequired()->cannotBeEmpty()->end()
            ->floatNode('min_score')->defaultValue(0.5)->end()
            ->end()
        ;
    }

    /**
     * @param array{enabled:bool, site_key:string, project_id:string, api_key:string, min_score:float} $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $services = $container->services();

        $services->set('artack_recaptcha_enterprise.ip_resolver')
            ->autowire()
            ->class(IpResolver::class)
        ;

        $services->set('artack_recaptcha_enterprise.user_agent_resolver')
            ->autowire()
            ->class(UserAgentResolver::class)
        ;

        $services->set('artack_recaptcha_enterprise.verifier')
            ->class(Verifier::class)
            ->args([
                $config['project_id'],
                $config['site_key'],
                $config['api_key'],
                service('artack_recaptcha_enterprise.ip_resolver'),
                service('artack_recaptcha_enterprise.user_agent_resolver'),
            ])
        ;

        $services->alias(VerifierInterface::class, 'artack_recaptcha_enterprise.verifier');

        $services->set(RecaptchaEnterpriseValidator::class)
            ->args([
                service('artack_recaptcha_enterprise.verifier'),
                $config['enabled'],
                $config['min_score'],
            ])
            ->tag('validator.constraint_validator')
        ;

        $services->set(RecaptchaEnterpriseType::class)
            ->args([
                $config['site_key'],
                $config['enabled'],
            ])
            ->tag('form.type')
        ;
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->prependExtensionConfig('twig', [
            'form_themes' => ['@RecaptchaEnterprise/Form/recaptcha_enterprise_widget.html.twig'],
        ]);
    }
}
