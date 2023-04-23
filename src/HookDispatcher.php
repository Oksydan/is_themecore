<?php

namespace Oksydan\Module\IsThemeCore;

use Oksydan\Module\IsThemeCore\Hook\AbstractHook;
use Oksydan\Module\IsThemeCore\Hook\Assets;
use Oksydan\Module\IsThemeCore\Hook\Header;
use Oksydan\Module\IsThemeCore\Hook\Htaccess;
use Oksydan\Module\IsThemeCore\Hook\HtmlOutput;
use Oksydan\Module\IsThemeCore\Hook\Smarty;

class HookDispatcher
{
    public const HOOK_CLASSES = [
        Header::class,
        Assets::class,
        Smarty::class,
        HtmlOutput::class,
        Htaccess::class,
    ];

    /**
     * Hook instances.
     *
     * @var AbstractHook[]
     */
    protected $hooks = [];

    public function __construct(\Is_themecore $module)
    {
        foreach (static::HOOK_CLASSES as $hookClass) {
            /** @var AbstractHook $hook */
            $hook = new $hookClass($module);
            $this->hooks[] = $hook;
        }
    }

    /**
     * Get available hooks
     *
     * @return string[]
     */
    public function getAvailableHooks()
    {
        $availableHooks = [];
        foreach ($this->hooks as $hook) {
            $availableHooks = array_merge($availableHooks, $hook->getAvailableHooks());
        }

        return $availableHooks;
    }

    public function dispatch($hookName, array $params = [])
    {
        foreach ($this->hooks as $hook) {
            if (method_exists($hook, $hookName)) {
                return $hook->{$hookName}($params);
            }
        }

        return false;
    }
}
