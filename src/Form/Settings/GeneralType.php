<?php


declare(strict_types=1);

namespace Oksydan\Module\IsThemeCore\Form\Settings;

use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
            ->add('list_display_settings', ChoiceType::class, [
                'choices' => $this->displayListChoices,
                'label' => $this->trans('Default list display', 'Modules.isthemecore.Admin'),
                'multistore_configuration_key' => GeneralConfiguration::THEMECORE_DISPLAY_LIST,
            ]);
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
