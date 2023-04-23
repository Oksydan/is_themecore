<?php

namespace Oksydan\Module\IsThemeCore\Hook;

use Oksydan\Module\IsThemeCore\Core\ThemeAssets\ThemeAssetConfigProvider;
use Oksydan\Module\IsThemeCore\Core\ThemeAssets\ThemeAssetsRegister;

class Assets extends AbstractHook
{
    public const HOOK_LIST = [
        'actionFrontControllerSetMedia',
        'actionProductSearchAfter',
    ];

    /**
     *  Removing ps_faceted search module assets
     */
    public function hookActionProductSearchAfter(): void
    {
        $this->context->controller->unregisterJavascript('facetedsearch_front');
        $this->context->controller->unregisterStylesheet('facetedsearch_front');

        $this->context->controller->unregisterJavascript('jquery-ui');
        $this->context->controller->unregisterStylesheet('jquery-ui');
        $this->context->controller->unregisterStylesheet('jquery-ui-theme');
    }

    public function hookActionFrontControllerSetMedia()
    {
        $assetsRegister = new ThemeAssetsRegister(
            new ThemeAssetConfigProvider(_PS_THEME_DIR_),
            $this->context
        );

        $assetsRegister->registerThemeAssets();

        \Media::addJsDef([
            'listDisplayAjaxUrl' => $this->context->link->getModuleLink($this->module->name, 'ajaxTheme'),
        ]);
    }
}
