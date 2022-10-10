<?php

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
use Oksydan\Module\IsThemeCore\Form\Settings\GeneralConfiguration;
use Oksydan\Module\IsThemeCore\Form\Settings\WebpConfiguration;

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
            'preloadCss' => \Configuration::get(GeneralConfiguration::THEMECORE_PRELOAD_CSS),
            'webpEnabled' => \Configuration::get(WebpConfiguration::THEMECORE_WEBP_ENABLED),
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
