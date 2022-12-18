<?php

namespace Oksydan\Module\IsThemeCore\Hook;

use Oksydan\Module\IsThemeCore\Core\ThemeAssets\ThemeAssetsRegister;
use Oksydan\Module\IsThemeCore\Core\ThemeAssets\ThemeAssetConfigProvider;
use Media;

class Assets extends AbstractHook
{
    const HOOK_LIST = [
      'actionFrontControllerSetMedia',
      'actionProductSearchAfter',
  	];

    /**
     *  Removing ps_faceted search module assets
     */
    public function hookActionProductSearchAfter() : void
    {
        $this->context->controller->unregisterJavascript('facetedsearch_front');
        $this->context->controller->unregisterStylesheet('facetedsearch_front');

        $this->context->controller->unregisterJavascript('jquery-ui');
        $this->context->controller->unregisterStylesheet('jquery-ui');
        $this->context->controller->unregisterStylesheet('jquery-ui-theme');
    }

    private function isListingPage() : bool
    {
        return $this->context->controller instanceof \ProductListingFrontControllerCore;
    }

    public function hookActionFrontControllerSetMedia()
    {
        $assetsRegister = new ThemeAssetsRegister(
            new ThemeAssetConfigProvider(_PS_THEME_DIR_),
            $this->context
        );

        $assetsRegister->registerThemeAssets();

        Media::addJsDef(array(
          'listDisplayAjaxUrl' => $this->context->link->getModuleLink($this->module->name, 'ajaxTheme')
        ));

        if ($this->isListingPage()) {
            $this->context->controller->registerJavascript(
                'themecore-listing',
                'modules/' . $this->module->name . '/views/js/front/listDisplay.js',
                [
                    'position' => 'bottom',
                    'priority' => 150
                ]
            );
        }
    }
}
