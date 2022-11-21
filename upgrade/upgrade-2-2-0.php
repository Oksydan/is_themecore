<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_2_0($module)
{
    return $module->registerHook('actionDispatcherBefore');

    return $module->registerHook('actionOutputHTMLBefore');
}
