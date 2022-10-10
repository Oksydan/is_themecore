<?php

declare(strict_types=1);

namespace Oksydan\Module\IsThemeCore\Form\Settings;

use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class WebpType extends TranslatorAwareType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $convertersList;

    /**
     * @var array
     */
    private $convertersListFull;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * WebpType constructor.
     *
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $convertersList
     * @param array $convertersListFull
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $convertersList,
        array $convertersListFull,
        RouterInterface $router
    ) {
        parent::__construct($translator, $locales);
        $this->convertersList = $convertersList;
        $this->convertersListFull = $convertersListFull;
        $this->router = $router;
    }

    private function allWebpConvertersDisabled(): bool
    {
        return array_reduce($this->convertersListFull, function ($carry, $item) {
            return $carry && $item['disabled'];
        }, true);
    }

    /**
     * {@inheritdoc}
     *
     * @param FormBuilderInterface<string, mixed> $builder
     * @param array<string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $webpDisabled = $this->allWebpConvertersDisabled();
        $extraAttributes = [];

        if ($webpDisabled) {
            $extraAttributes = [
                'alert_message' => $this->trans('Webp converters not available contact your admin or hosting provider.', 'Modules.isthemecore.Admin'),
                'alert_type' => 'danger',
                'alert_position' => 'append',
            ];
        }

        $builder
            ->add('webp_enabled',
                SwitchType::class,
                array_merge(
                    [
                        'required' => false,
                        'label' => $this->trans('Enable WEBP', 'Modules.isthemecore.Admin'),
                        'disabled' => $webpDisabled,
                    ],
                    $extraAttributes
                )
            )
            ->add('webp_sharpyuv',
                SwitchType::class,
                [
                    'required' => false,
                    'label' => $this->trans('Enable better RGB->YUV color conversion', 'Modules.isthemecore.Admin'),
                    'disabled' => $webpDisabled,
                ]
            )
            ->add('webp_quality',
                TextType::class,
                [
                    'required' => false,
                    'label' => $this->trans('Webp quality', 'Modules.isthemecore.Admin'),
                    'help' => $this->trans('Range 1-100', 'Modules.isthemecore.Admin'),
                    'disabled' => $webpDisabled,
                    'constraints' => [
                        $this->getRangeConstraint(1, 100),
                        $this->getNotBlankConstraint(),
                    ],
                ]
            )
            ->add('webp_converter',
                ChoiceType::class,
                [
                    'choices' => $this->convertersList,
                    'label' => $this->trans('Webp converter options', 'Modules.isthemecore.Admin'),
                    'disabled' => $webpDisabled,
                    'expanded' => true,
                    'multiple' => false,
                    'choice_attr' => function ($choice) {
                        return ['disabled' => $this->convertersListFull[$choice]['disabled']];
                    },
                    'choice_label' => function ($choice) {
                        return $this->convertersListFull[$choice]['label'] . ($this->convertersListFull[$choice]['disabled'] ? '<span class="ml-1 badge badge-danger">' . $this->trans('not available', 'Modules.isthemecore.Admin') . '</span>' : '');
                    },
                ]
            )
            ->add('erase_all_webp', IconButtonType::class, [
                'label' => $this->trans('Erase all webp images', 'Modules.isthemecore.Admin'),
                'type' => 'link',
                'icon' => 'delete',
                'attr' => [
                    'class' => 'btn-danger',
                    'href' => $this->router->generate(
                        'is_themecore_module_settings_webp_erase_all',
                        [
                            'type' => 'all',
                        ]
                    ),
                ],
            ])
            ->add('erase_product_webp', IconButtonType::class, [
                'label' => $this->trans('Erase all product webp images', 'Modules.isthemecore.Admin'),
                'type' => 'link',
                'icon' => 'delete',
                'attr' => [
                    'class' => 'btn-danger',
                    'href' => $this->router->generate(
                        'is_themecore_module_settings_webp_erase_all',
                        [
                            'type' => 'product',
                        ]
                    ),
                ],
            ])
            ->add('erase_modules_webp', IconButtonType::class, [
                'label' => $this->trans('Erase all modules webp images', 'Modules.isthemecore.Admin'),
                'type' => 'link',
                'icon' => 'delete',
                'attr' => [
                    'class' => 'btn-danger',
                    'href' => $this->router->generate(
                        'is_themecore_module_settings_webp_erase_all',
                        [
                            'type' => 'module',
                        ]
                    ),
                ],
            ])
            ->add('erase_cms_webp', IconButtonType::class, [
                'label' => $this->trans('Erase all CMS webp images', 'Modules.isthemecore.Admin'),
                'type' => 'link',
                'icon' => 'delete',
                'attr' => [
                    'class' => 'btn-danger',
                    'href' => $this->router->generate(
                        'is_themecore_module_settings_webp_erase_all',
                        [
                            'type' => 'cms',
                        ]
                    ),
                ],
            ])
            ->add('erase_themes_webp', IconButtonType::class, [
                'label' => $this->trans('Erase all themes webp images', 'Modules.isthemecore.Admin'),
                'type' => 'link',
                'icon' => 'delete',
                'attr' => [
                    'class' => 'btn-danger',
                    'href' => $this->router->generate(
                        'is_themecore_module_settings_webp_erase_all',
                        [
                            'type' => 'themes',
                        ]
                    ),
                ],
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
                ]
            ),
        ]);
    }
}
