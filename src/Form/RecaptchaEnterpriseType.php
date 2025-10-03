<?php

declare(strict_types=1);

namespace Artack\RecaptchaEnterpriseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
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
    ) {}

    public function getParent(): string
    {
        return HiddenType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // no server options; token set by JS
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['site_key'] = $this->siteKey;
        $view->vars['action'] = $options['action'];
        $view->vars['enabled'] = $options['enabled'];
        $view->vars['locale'] = $options['locale'];

        if (isset($options['script_nonce_csp'])) {
            $view->vars['script_nonce_csp'] = $options['script_nonce_csp'];
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'action' => null,
            'enabled' => true,
            'locale' => 'en',
            'script_nonce_csp' => null,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'recaptcha_enterprise';
    }
}
