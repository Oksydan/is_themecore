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

use Oksydan\Module\IsThemeCore\Hook\AbstractHook;
use Oksydan\Module\IsThemeCore\Core\ListingDisplay\ThemeListDisplay;
use Oksydan\Module\IsThemeCore\Core\Breadcrumbs\ThemeBreadcrumbs;
use Oksydan\Module\IsThemeCore\Core\StructuredData\Provider\StructuredDataProductProvider;
use Oksydan\Module\IsThemeCore\Core\StructuredData\Presenter\StructuredDataProductPresenter;
use Oksydan\Module\IsThemeCore\Core\StructuredData\Provider\StructuredDataBreadcrumbProvider;
use Oksydan\Module\IsThemeCore\Core\StructuredData\Presenter\StructuredDataBreadcrumbPresenter;
use Oksydan\Module\IsThemeCore\Core\StructuredData\Provider\StructuredDataShopProvider;
use Oksydan\Module\IsThemeCore\Core\StructuredData\Presenter\StructuredDataShopPresenter;
use Oksydan\Module\IsThemeCore\Core\StructuredData\Provider\StructuredDataWebsiteProvider;
use Oksydan\Module\IsThemeCore\Core\StructuredData\Presenter\StructuredDataWebsitePresenter;
use Oksydan\Module\IsThemeCore\Core\StructuredData\StructuredData;

class Header extends AbstractHook
{
    const HOOK_LIST = [
        'displayHeader',
    ];

    public function hookDisplayHeader() : string
    {
        $themeListDisplay = new ThemeListDisplay();
        $breadcrumbs = (new ThemeBreadcrumbs())->getBreadcrumb();

        if ($breadcrumbs['count']) {
            $this->context->smarty->assign([
                'breadcrumb' => $breadcrumbs
            ]);
        }

        $this->context->smarty->assign([
            'listingDisplayType' => $themeListDisplay->getDisplay(),
            'jsonData' => $this->getStructuredData(),
        ]);

        return $this->module->fetch('module:is_themecore/views/templates/hook/head.tpl');
    }

    public function getStructuredData() : array
    {
        $dataArray = [];

        if ($this->context->controller instanceof \ProductControllerCore) {
            $dataArray[] = (new StructuredData(
                new StructuredDataProductProvider($this->context),
                new StructuredDataProductPresenter($this->context)
            ))->getFormattedData();
        }

        $dataArray[] = (new StructuredData(
            new StructuredDataBreadcrumbProvider($this->context),
            new StructuredDataBreadcrumbPresenter()
        ))->getFormattedData();

        $dataArray[] = (new StructuredData(
            new StructuredDataShopProvider($this->context),
            new StructuredDataShopPresenter($this->context)
        ))->getFormattedData();

        if ($this->context->controller->getPageName() == 'index') {
            $dataArray[] = (new StructuredData(
                new StructuredDataWebsiteProvider($this->context),
                new StructuredDataWebsitePresenter($this->context)
            ))->getFormattedData();
        }

        return $dataArray;
    }
}
