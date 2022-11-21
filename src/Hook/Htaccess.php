<?php

namespace Oksydan\Module\IsThemeCore\Hook;

class Htaccess extends AbstractHook
{
    public const HOOK_LIST = [
        'actionHtaccessCreate',
        'objectShopUrlAddAfter',
        'objectShopUrlUpdateAfter',
        'objectShopUrlDeleteAfter',
    ];

    public function hookActionHtaccessCreate()
    {
        $generator = $this->module->get('oksydan.module.is_themecore.core.htaccess.htaccess_generator');

        $generator->generate();
        $generator->writeFile();
    }

    public function hookObjectShopUrlAddAfter()
    {
        $this->hookActionHtaccessCreate();
    }

    public function hookObjectShopUrlUpdateAfter()
    {
        $this->hookActionHtaccessCreate();
    }

    public function hookObjectShopUrlDeleteAfter()
    {
        $this->hookActionHtaccessCreate();
    }
}
