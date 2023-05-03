<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(dirname(__FILE__) . '../vendor/autoload.php')) {
    require_once dirname(__FILE__) . '../vendor/autoload.php';
}

use Oksydan\Module\IsThemeCore\Form\Settings\GeneralConfiguration;

function upgrade_module_4_1_0($module)
{
    $success = $module->installPartytown();
    $success &= Configuration::updateValue(GeneralConfiguration::THEMECORE_LOAD_PARTY_TOWN, false);
    $success &= Configuration::updateValue(GeneralConfiguration::THEMECORE_DEBUG_PARTY_TOWN, false);

    return $success;
}
