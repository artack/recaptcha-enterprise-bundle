artack/recaptcha-enterprise-bundle
=================================

> Symfony integration for Google reCAPTCHA Enterprise (Assessments API).


[![Latest Release](https://img.shields.io/packagist/v/artack/recaptcha-enterprise-bundle.svg)](https://packagist.org/packages/artack/recaptcha-enterprise-bundle)
[![MIT License](https://img.shields.io/packagist/l/artack/recaptcha-enterprise-bundle.svg)](http://opensource.org/licenses/MIT)
[![Total Downloads](https://img.shields.io/packagist/dt/artack/recaptcha-enterprise-bundle.svg)](https://packagist.org/packages/artack/recaptcha-enterprise-bundle)

Developed by [ARTACK WebLab GmbH](https://www.artack.ch) in Zurich, Switzerland.


Features
--------

- Provides the **RecaptchaEnterpriseType** form type that renders the hidden token field, loads the Google script and submits the token transparently.
- Ships a **RecaptchaEnterprise** validation constraint for attributes and PHP configuration, including configurable score threshold and action names.
- Automatically resolves client IP and User-Agent from Symfony's request stack and forwards them to Google when available.
- Registers the form theme automatically, so no manual Twig configuration is required.


Installation
------------

Install the bundle via [Composer](https://getcomposer.org):

```shell
$ composer require artack/recaptcha-enterprise-bundle
```

The bundle is auto-registered thanks to Symfony Flex support.


Configuration
-------------

Create `config/packages/artack_recaptcha_enterprise.yaml` with your Google project credentials:

```yaml
# config/packages/artack_recaptcha_enterprise.yaml
artack_recaptcha_enterprise:
    enabled: '%env(resolve:ARTACK_GOOGLE_RECAPTCHA_ENABLED)%' # defaults to true, use this to disable validation in dev and test environments
    site_key: '%env(resolve:ARTACK_GOOGLE_RECAPTCHA_SITE_KEY)%'
    project_id: '%env(resolve:ARTACK_GOOGLE_RECAPTCHA_PROJECT_ID)%'
    api_key: '%env(resolve:ARTACK_GOOGLE_RECAPTCHA_API_KEY)%'
    min_score: 0.5 # default score threshold used by the validator when none is provided
```

All keys are required. `min_score` defaults to `0.5` and is used when a constraint does not define its own threshold.


Usage
-----

Render the token field in a Symfony form:

```php
use Artack\RecaptchaEnterpriseBundle\Form\RecaptchaEnterpriseType;
use Artack\RecaptchaEnterpriseBundle\Validator\RecaptchaEnterprise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

final class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('message', TextareaType::class)
            ->add('recaptchaToken', RecaptchaEnterpriseType::class, [
                'action_name' => 'contact',  # sent to Google; also matched when validating
                'script_csp_nonce' => '...' # optional generated nonce to be used in the script tag
                'constraints' => [
                    new RecaptchaEnterprise(
                        minScore: 0.7, # optional
                        actionName: 'contact',
                    ),
                ],
            ]);
    }
}
```

The provided Twig theme is prepended automatically. When the form submits, the bundle executes `grecaptcha.enterprise.execute`, fills the hidden field and re-submits the form.

License
-------

This bundle is released under the [MIT License](LICENSE).
