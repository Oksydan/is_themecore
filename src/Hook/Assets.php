<?php
/**
 * Copyright 2021-2022 InPost S.A.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the EUPL-1.2 or later.
 * You may not use this work except in compliance with the Licence.
 *
 * You may obtain a copy of the Licence at:
 * https://joinup.ec.europa.eu/software/page/eupl
 * It is also bundled with this package in the file LICENSE.txt
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the Licence is distributed on an AS IS basis,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the Licence for the specific language governing permissions
 * and limitations under the Licence.
 *
 * @author    InPost S.A.
 * @copyright 2021-2022 InPost S.A.
 * @license   https://joinup.ec.europa.eu/software/page/eupl
 */

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

    public function hookActionFrontControllerSetMedia()
    {
        $listingPages = ['category', 'prices-drop', 'new-products', 'bestsales', 'manufacturer', 'search'];
        $pageName = $this->context->controller->getPageName();

        $assetsRegister = new ThemeAssetsRegister(
            new ThemeAssetConfigProvider(_PS_THEME_DIR_),
            $this->context
        );

        $assetsRegister->registerThemeAssets();

        Media::addJsDef(array(
          'listDisplayAjaxUrl' => $this->context->link->getModuleLink($this->module->name, 'ajaxTheme')
      ));

        if (in_array($pageName, $listingPages)) {
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
