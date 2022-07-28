<?php


declare(strict_types=1);

namespace Oksydan\Module\IsThemeCore\Form\Settings;

use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

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
                    'help' => $this->trans('Early hints have to be supported by your web server. Enabling it w/o web server support will make no effect.', 'Modules.isthemecore.Admin'),
                ]
            )
            ->add('preload_css',
                SwitchType::class,
                [
                    'required' => false,
                    'label' => $this->trans('Preload css enabled', 'Modules.isthemecore.Admin'),
                ]
            )
            ->add('webp_enabled',
                SwitchType::class,
                [
                    'required' => false,
                    'label' => $this->trans('Enable WEBP', 'Modules.isthemecore.Admin'),
                ]
            )
            ->add('webp_quality',
                TextType::class,
                [
                    'required' => false,
                    'label' => $this->trans('Webp quality', 'Modules.isthemecore.Admin'),
                    'help' => $this->trans('Range 1-100', 'Modules.isthemecore.Admin'),
                    'constraints' => [
                        $this->getRangeConstraint(1, 100),
                        $this->getNotBlankConstraint(),
                    ],
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

    /**
     * @return NotBlank
     */
    private function getNotBlankConstraint()
    {
        return new NotBlank([
            'message' => $this->trans('This field cannot be empty.', 'Modules.isthemecore.Admin'),
        ]);
    }

    /**
     * @return Range
     */
    private function getRangeConstraint(int $min = 1, int $max = 100)
    {
        return new Range([
            'min' => $min,
            'max' => $max,
            'invalidMessage' => $this->trans(
                'This field value have to be between %min% and %max%.',
                'Modules.isthemecore.Admin',
                [
                    '%min%' => $min,
                    '%max%' => $max,
                ],
            ),
        ]);
    }
}
