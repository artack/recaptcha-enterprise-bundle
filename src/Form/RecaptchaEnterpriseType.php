<?php

declare(strict_types=1);

namespace Artack\RecaptchaEnterpriseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<null>
 */
final class RecaptchaEnterpriseType extends AbstractType
{
    public function __construct(
        private readonly string $siteKey,
        private readonly bool $enabled,
    ) {}

    public function getParent(): string
    {
        return HiddenType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['site_key'] = $this->siteKey;
        $view->vars['action_name'] = $options['action_name'];
        $view->vars['enabled'] = $this->enabled;
        $view->vars['locale'] = $options['locale'];
        $view->vars['script_csp_nonce'] = $options['script_csp_nonce'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'mapped' => false,
            'action_name' => null,
            'locale' => 'en',
            'script_csp_nonce' => null,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'recaptcha_enterprise';
    }
}
