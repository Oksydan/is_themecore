<?php

declare(strict_types=1);

namespace Oksydan\Module\IsThemeCore\Form\Settings;

use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class GeneralType extends TranslatorAwareType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $displayListChoices;

    /**
     * GeneralType constructor.
     *
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $displayListChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $displayListChoices
    ) {
        parent::__construct($translator, $locales);
        $this->displayListChoices = $displayListChoices;
    }

    /**
     * {@inheritdoc}
     *
     * @param FormBuilderInterface<string, mixed> $builder
     * @param array<string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('list_display_settings',
                ChoiceType::class,
                [
                    'choices' => $this->displayListChoices,
                    'label' => $this->trans('Default list display', 'Modules.isthemecore.Admin'),
                    'multistore_configuration_key' => GeneralConfiguration::THEMECORE_DISPLAY_LIST,
                ]
            )
            ->add('early_hints',
                SwitchType::class,
                [
                    'required' => false,
                    'label' => $this->trans('Early hints (HTTP 103) enabled', 'Modules.isthemecore.Admin'),
                    'help' => $this->trans('Cloudflare CDN, Early hints option have to enabled. <a href="https://developers.cloudflare.com/cache/about/early-hints/">More information</a>', 'Modules.isthemecore.Admin'),
                    'multistore_configuration_key' => GeneralConfiguration::THEMECORE_EARLY_HINTS,
                ]
            )
            ->add('preload_css',
                SwitchType::class,
                [
                    'required' => false,
                    'label' => $this->trans('Preload css enabled, only working with CCC for css option enabled', 'Modules.isthemecore.Admin'),
                    'multistore_configuration_key' => GeneralConfiguration::THEMECORE_PRELOAD_CSS,
                ]
            )
            ->add('load_party_town',
                SwitchType::class,
                [
                    'required' => false,
                    'label' => $this->trans('Load partytown script', 'Modules.isthemecore.Admin'),
                    'help' => $this->trans('Be aware that partytown is still beta. Make sure that everything is working as expected before pushing it to your production store.', 'Modules.isthemecore.Admin'),
                    'multistore_configuration_key' => GeneralConfiguration::THEMECORE_LOAD_PARTY_TOWN,
                ]
            )
            ->add('debug_party_town',
                SwitchType::class,
                [
                    'required' => false,
                    'label' => $this->trans('Enable debug mode for partytown', 'Modules.isthemecore.Admin'),
                    'multistore_configuration_key' => GeneralConfiguration::THEMECORE_DEBUG_PARTY_TOWN,
                ]
            );
    }

    /**
     * {@inheritdoc}
     *
     * @see MultistoreConfigurationTypeExtension
     */
    public function getParent(): string
    {
        return MultistoreConfigurationType::class;
    }
}
