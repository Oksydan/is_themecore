<?php

namespace Oksydan\Module\IsThemeCore\Core\Partytown;

class PartytownScript
{
    private \Is_themecore $module;

    public function __construct(
        \Is_themecore $module
    ) {
        $this->module = $module;
    }

    public function getScriptPath(): string
    {
        return _PS_MODULE_DIR_ . $this->module->name . '/public/partytown.js';
    }

    public function getScriptUri(): string
    {
        return $this->module->getPathUri() . '/public/partytown.js';
    }

    public function getScriptContent(): string
    {
        $script = '';
        $filePath = $this->getScriptPath();

        if (file_exists($filePath)) {
            $script = file_get_contents($filePath);
        }

        return $script;
    }
}
