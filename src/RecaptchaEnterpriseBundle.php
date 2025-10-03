<?php

declare(strict_types=1);

namespace Artack\RecaptchaEnterpriseBundle;

use Artack\RecaptchaEnterpriseBundle\Form\RecaptchaEnterpriseType;
use Artack\RecaptchaEnterpriseBundle\Service\IpResolver;
use Artack\RecaptchaEnterpriseBundle\Service\UserAgentResolver;
use Artack\RecaptchaEnterpriseBundle\Service\Verifier;
use Artack\RecaptchaEnterpriseBundle\Validator\RecaptchaEnterpriseValidator;
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
            ->scalarNode('site_key')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('project_id')->isRequired()->cannotBeEmpty()->end()
            ->scalarNode('api_key')->isRequired()->cannotBeEmpty()->end()
            ->floatNode('min_score')->defaultValue(0.5)->end()
            ->end()
        ;
    }

    /**
     * @param array{site_key:string, project_id:string, api_key:string, min_score:float} $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $services = $container->services();

        $services->set('artack_recaptcha_enterprise.ip_resolver')
            ->class(IpResolver::class)
        ;

        $services->set('artack_recaptcha_enterprise.user_agent_resolver')
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

        $services->set(RecaptchaEnterpriseValidator::class)
            ->args([
                service('artack_recaptcha_enterprise.verifier'),
                $config['min_score'],
            ])
            ->tag('validator.constraint_validator')
        ;

        $services->set(RecaptchaEnterpriseType::class)
            ->args([
                $config['site_key'],
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
