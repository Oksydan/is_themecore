<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(dirname(__FILE__) . '../vendor/autoload.php')) {
    require_once dirname(__FILE__) . '../vendor/autoload.php';
}

function upgrade_module_4_1_2($module)
{
    return $module->registerHook('actionFrontControllerInitBefore');
}
