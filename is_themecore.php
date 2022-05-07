<?php

declare(strict_types=1);

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

use Oksydan\Module\IsThemeCore\Form\Settings\GeneralConfiguration;
use Oksydan\Module\IsThemeCore\Core\Smarty\SmartyHelperFunctions;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Oksydan\Module\IsThemeCore\Core\ListingDisplay\ThemeListDisplay;
use Oksydan\Module\IsThemeCore\Core\Breadcrumbs\ThemeBreadcrumbs;
use Oksydan\Module\IsThemeCore\Core\ThemeAssets\ThemeAssetsRegister;
use Oksydan\Module\IsThemeCore\Core\ThemeAssets\ThemeAssetConfigProvider;
use Oksydan\Module\IsThemeCore\Core\StructuredData\Provider\StructuredDataProductProvider;
use Oksydan\Module\IsThemeCore\Core\StructuredData\Presenter\StructuredDataProductPresenter;
use Oksydan\Module\IsThemeCore\Core\StructuredData\Provider\StructuredDataBreadcrumbProvider;
use Oksydan\Module\IsThemeCore\Core\StructuredData\Presenter\StructuredDataBreadcrumbPresenter;
use Oksydan\Module\IsThemeCore\Core\StructuredData\Provider\StructuredDataShopProvider;
use Oksydan\Module\IsThemeCore\Core\StructuredData\Presenter\StructuredDataShopPresenter;
use Oksydan\Module\IsThemeCore\Core\StructuredData\Provider\StructuredDataWebsiteProvider;
use Oksydan\Module\IsThemeCore\Core\StructuredData\Presenter\StructuredDataWebsitePresenter;
use Oksydan\Module\IsThemeCore\Core\StructuredData\StructuredData;

class is_themecore extends Module
{
    /**
     * @var array<string, string> Configuration values
     */
    public const CONFIGURATION_VALUES = [
        GeneralConfiguration::THEMECORE_DISPLAY_LIST => 'grid',
    ];

    /**
     * @var string[] Hooks to register
     */
    public const HOOKS = [
        'actionDispatcher',
        'actionFrontControllerSetMedia',
        'displayListingStructuredData',
        'displayHeader',
        'actionProductSearchAfter',
    ];

    /**
     * @var Configuration<string, mixed> Configuration
     */
    private $configuration;

    public function __construct()
    {
        $this->name = 'is_themecore';
        $this->tab = 'others';
        $this->version = '2.0.0';
        $this->author = 'Igor Stępień';
        $this->ps_versions_compliancy = ['min' => '1.7.8.0', 'max' => _PS_VERSION_];

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Theme core module',
            [],
            'Modules.isthemecore.Admin'
        );
        $this->description = $this->trans('Required for theme to work.',
            [],
            'Modules.isthemecore.Admin'
        );

        $this->tabs = [
            [
                'name' => 'Theme core module settings',
                'class_name' => 'themecoreSettings',
                'route_name' => 'is_themecore_module_settings',
                'parent_class_name' => 'CONFIGURE',
                'visible' => false,
                'wording' => 'Theme core module settings',
                'wording_domain' => 'Modules.isthemecore.Admin',
            ],
        ];

        $this->configuration = new Configuration();
    }

    /**
     * {@inheritdoc}
     */
    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function install(): bool
    {
        return parent::install()
            && $this->installConfiguration()
            && $this->registerHook(self::HOOKS)
        ;
    }

    /**
     * Install configuration values
     */
    private function installConfiguration(): bool
    {
        try {
            foreach (self::CONFIGURATION_VALUES as $key => $default_value) {
                $this->configuration->set($key, $default_value);
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall(): bool
    {
        return parent::uninstall()
            && $this->uninstallConfiguration()
        ;
    }

    /**
     * Uninstall configuration values
     */
    private function uninstallConfiguration(): bool
    {
        try {
            foreach (array_keys(self::CONFIGURATION_VALUES) as $key) {
                $this->configuration->remove($key);
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Get module configuration page content
     */
    public function getContent(): void
    {
        $container = SymfonyContainer::getInstance();

        if ($container != null) {
            /** @var UrlGeneratorInterface */
            $router = $container->get('router');

            Tools::redirectAdmin($router->generate('is_themecore_module_settings'));
        }
    }

    public function hookActionDispatcher() : void
    {
        $this->context->smarty->registerPlugin('function', 'generateImagesSources', ['Oksydan\Module\IsThemeCore\Core\Smarty\SmartyHelperFunctions', 'generateImagesSources']);
        $this->context->smarty->registerPlugin('function', 'generateImageSvgPlaceholder', ['Oksydan\Module\IsThemeCore\Core\Smarty\SmartyHelperFunctions', 'generateImageSvgPlaceholder']);
    }

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

        return $this->fetch('module:is_themecore/views/templates/hook/head.tpl');
    }

    public function getStructuredData() : array
    {
        $dataArray = [];

        if ($this->context->controller instanceof ProductControllerCore) {
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
        $listingPages = ['category', 'pricesdrop', 'new-products', 'bestsales', 'manufacturer', 'search'];
        $pageName = $this->context->controller->getPageName();

        $assetsRegister = new ThemeAssetsRegister(
            new ThemeAssetConfigProvider(_PS_THEME_DIR_),
            $this->context
        );

        $assetsRegister->registerThemeAssets();

        Media::addJsDef(array(
            'listDisplayAjaxUrl' => $this->context->link->getModuleLink($this->name, 'ajaxTheme')
        ));

        if(in_array($pageName, $listingPages)) {
            $this->context->controller->registerJavascript(
                'themecore-listing',
                'modules/' . $this->name . '/views/js/front/listDisplay.js',
                [
                    'position' => 'bottom',
                    'priority' => 150
                ]
            );
        }
    }

}
