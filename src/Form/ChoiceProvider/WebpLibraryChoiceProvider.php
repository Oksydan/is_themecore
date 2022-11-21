<?php

namespace Oksydan\Module\IsThemeCore\Form\ChoiceProvider;

use Oksydan\Module\IsThemeCore\Core\Webp\WebpConvertLibraries;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

class WebpLibraryChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var WebpConvertLibraries
     */
    protected $webpConvertLibraries;

    /**
     * @param WebpConvertLibraries $webpConvertLibraries
     */
    public function __construct(WebpConvertLibraries $webpConvertLibraries)
    {
        $this->webpConvertLibraries = $webpConvertLibraries;
    }

    /**
     * @return array
     */
    public function getChoices(): array
    {
        $choices = [];

        foreach ($this->webpConvertLibraries->getConvertersList() as $converter) {
            $choices[$converter['label']] = $converter['id'];
        }

        return $choices;
    }

    /**
     * @return array
     */
    public function getChoicesFull(): array
    {
        $choices = [];

        foreach ($this->webpConvertLibraries->getConvertersList() as $converter) {
            $choices[$converter['id']] = $converter;
        }

        return $choices;
    }
}
