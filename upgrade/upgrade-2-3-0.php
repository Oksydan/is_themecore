<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_3_0($module)
{
    $res = $module->registerHook('actionHtaccessCreate');
    $res &= $module->registerHook('objectShopUrlAddAfter');
    $res &= $module->registerHook('objectShopUrlUpdateAfter');
    $res &= $module->registerHook('objectShopUrlDeleteAfter');

    return $res;
}
