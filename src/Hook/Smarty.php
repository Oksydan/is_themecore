<?php

namespace Oksydan\Module\IsThemeCore\Hook;

class Smarty extends AbstractHook
{
    public const HOOK_LIST = [
        'actionDispatcherBefore',
    ];

    public function hookActionDispatcherBefore(): void
    {
        $this->context->smarty->registerPlugin('function', 'generateImagesSources', ['Oksydan\Module\IsThemeCore\Core\Smarty\SmartyHelperFunctions', 'generateImagesSources']);
        $this->context->smarty->registerPlugin('function', 'generateImageSvgPlaceholder', ['Oksydan\Module\IsThemeCore\Core\Smarty\SmartyHelperFunctions', 'generateImageSvgPlaceholder']);
        $this->context->smarty->registerPlugin('function', 'appendParamToUrl', ['Oksydan\Module\IsThemeCore\Core\Smarty\SmartyHelperFunctions', 'appendParamToUrl']);
        $this->context->smarty->registerPlugin('block', 'images_block', ['Oksydan\Module\IsThemeCore\Core\Smarty\SmartyHelperFunctions', 'imagesBlock']);
        $this->context->smarty->registerPlugin('block', 'cms_images_block', ['Oksydan\Module\IsThemeCore\Core\Smarty\SmartyHelperFunctions', 'cmsImagesBlock']);
        $this->context->smarty->registerPlugin('block', 'display_mobile', ['Oksydan\Module\IsThemeCore\Core\Smarty\SmartyHelperFunctions', 'displayMobileBlock']);
        $this->context->smarty->registerPlugin('block', 'display_desktop', ['Oksydan\Module\IsThemeCore\Core\Smarty\SmartyHelperFunctions', 'displayDesktopBlock']);
    }
}
