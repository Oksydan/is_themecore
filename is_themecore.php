<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__) . '/classes/ThemeAssets.php');
require_once(dirname(__FILE__) . '/classes/ThemeBreadcrumbs.php');
require_once(dirname(__FILE__) . '/classes/ThemeListDisplay.php');
require_once(dirname(__FILE__) . '/classes/SmartyHelperFunctions.php');
require_once(dirname(__FILE__) . '/classes/ThemeStructuredJsonData.php');

class Is_themecore extends Module
{

    public function __construct()
    {
        $this->name = 'is_themecore';
        $this->author = 'Igor Stępień';
        $this->version = '1.1.2';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Theme core module');
        $this->description = $this->l('Required for theme to work.');

        $this->ps_versions_compliancy = array('min' => '1.7.4.0', 'max' => _PS_VERSION_);

        $this->themeAssetsObject = null;
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHooks() &&
            $this->installDefaultConfig();
    }

    public function installDefaultConfig()
    {
        Configuration::updateValue('THEMECORE_DISPLAY_LIST', 'grid');
        return true;
    }

    public function registerHooks()
    {
        $return = true;
        $return &= $this->registerHook('actionDispatcher');
        $return &= $this->registerHook('actionFrontControllerSetMedia');
        $return &= $this->registerHook('displayListingStructuredData');
        $return &= $this->registerHook('displayHeader');
        $return &= $this->registerHook('actionProductSearchAfter');
        return $return;
    }

    public function hookActionFrontControllerSetMedia()
    {
        $listingPages = ['category', 'pricesdrop', 'newproducts', 'bestsales', 'manufacturer', 'search'];
        $pageName = Tools::getValue('controller');

        $this->themeAssetsObject = new ThemeAssets($pageName, 'starter', $this->context);
        $this->themeAssetsObject->setThemeAssets();

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

    /*
    *  Removing ps_faceted search module assets
    */
    public function hookActionProductSearchAfter()
    {
        if ($this->themeAssetsObject) {
            $this->themeAssetsObject->unregisterPsFacetedSearchAssets();
        }
    }

    public function hookActionDispatcher()
    {
        $this->context->smarty->registerPlugin('function', 'generateImagesSources', array('SmartyHelperFunctions', 'generateImagesSources'));
        $this->context->smarty->registerPlugin('function', 'generateImageSvgPlaceholder', array('SmartyHelperFunctions', 'generateImageSvgPlaceholder'));
    }

    public function hookDisplayHeader()
    {
        $breadcrumbs = (new ThemeBreadcrumbs())->getBreadcrumb();
        $jsonObj = new ThemeStructuredJsonData();
        $jsonData = $jsonObj->getJsonData();

        if ($breadcrumbs['count']) {
            $this->context->smarty->assign('breadcrumb', $breadcrumbs);
        }

        $this->context->smarty->assign([
            'listingDisplayType' => ThemeListDisplay::getDisplay(),
            'jsonData' => $jsonData
        ]);

        return $this->fetch('module:is_themecore/views/template/hook/head.tpl');
    }

    public function hookDisplayListingStructuredData($params)
    {
        if (empty($params['listing'])) {
            return;
        }

        $jsonObj = new ThemeStructuredJsonData();
        $jsonObj->getListingData($params['listing']);
        $jsonData = $jsonObj->getJsonData();

        $this->context->smarty->assign([
            'jsonData' => $jsonData
        ]);

        return $this->fetch('module:is_themecore/views/template/hook/jsonData.tpl');
    }


    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminThemeCoreConfiguration'));
    }

}
