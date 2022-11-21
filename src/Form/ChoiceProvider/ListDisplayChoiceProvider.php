<?php

namespace Oksydan\Module\IsThemeCore\Form\ChoiceProvider;

use Oksydan\Module\IsThemeCore\Core\ListingDisplay\ThemeListDisplay;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

class ListDisplayChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var ThemeListDisplay
     */
    protected $themeListDisplay;

    /**
     * @param ThemeListDisplay $themeListDisplay
     */
    public function __construct(ThemeListDisplay $themeListDisplay)
    {
        $this->themeListDisplay = $themeListDisplay;
    }

    /**
     * @return array
     */
    public function getChoices(): array
    {
        $choices = [];

        foreach ($this->themeListDisplay->getDisplayOptions() as $display) {
            $choices[$display] = $display;
        }

        return $choices;
    }
}
