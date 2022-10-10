<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(dirname(__FILE__) . '../vendor/autoload.php')) {
    require_once dirname(__FILE__) . '../vendor/autoload.php';
}

use Oksydan\Module\IsThemeCore\Form\Settings\WebpConfiguration;

function upgrade_module_2_4_0($module)
{
    Configuration::updateValue(WebpConfiguration::THEMECORE_WEBP_ENABLED, false);
    Configuration::updateValue(WebpConfiguration::THEMECORE_WEBP_QUALITY, 90);
    Configuration::updateValue(WebpConfiguration::THEMECORE_WEBP_SHARPYUV, true);

    return true;
}
