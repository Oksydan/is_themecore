<?php

declare(strict_types=1);

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

use Oksydan\Module\IsThemeCore\Core\Partytown\FilesInstallation;
use Oksydan\Module\IsThemeCore\Form\Settings\GeneralConfiguration;
use Oksydan\Module\IsThemeCore\Form\Settings\WebpConfiguration;
use Oksydan\Module\IsThemeCore\HookDispatcher;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Is_themecore extends Module
{
    protected $hookDispatcher;

    /**
     * @var array<string, string> Configuration values
     */
    public const CONFIGURATION_VALUES = [
        GeneralConfiguration::THEMECORE_DISPLAY_LIST => 'grid',
        GeneralConfiguration::THEMECORE_EARLY_HINTS => false,
        GeneralConfiguration::THEMECORE_PRELOAD_CSS => false,
        GeneralConfiguration::THEMECORE_LOAD_PARTY_TOWN => false,
        GeneralConfiguration::THEMECORE_DEBUG_PARTY_TOWN => false,
        WebpConfiguration::THEMECORE_WEBP_ENABLED => false,
        WebpConfiguration::THEMECORE_WEBP_QUALITY => 90,
        WebpConfiguration::THEMECORE_WEBP_CONVERTER => null,
        WebpConfiguration::THEMECORE_WEBP_SHARPYUV => false,
    ];

    /**
     * @var string[] Hooks to register
     */
    public const HOOKS = [
        'actionOutputHTMLBefore',
        'actionDispatcherBefore',
        'actionFrontControllerSetMedia',
        'displayListingStructuredData',
        'displayHeader',
        'actionProductSearchAfter',
        'actionHtaccessCreate',
        'objectShopUrlAddAfter',
        'objectShopUrlUpdateAfter',
        'objectShopUrlDeleteAfter',
    ];

    /**
     * @var Configuration<string, mixed> Configuration
     */
    private $configuration;

    public function __construct()
    {
        $this->name = 'is_themecore';
        $this->tab = 'others';
        $this->version = '4.1.0';
        $this->author = 'Igor Stępień';
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans(
            'Theme core module',
            [],
            'Modules.isthemecore.Admin'
        );
        $this->description = $this->trans(
            'Required for theme to work.',
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
            && $this->installPartytown()
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
     * Install Partytown
     */
    private function installPartytown(): bool
    {
        $installer = new FilesInstallation($this); // SERVICES NOT AVAILABLE DURING INSTALLATION

        try {
            $installer->installFiles();
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

    public function __call(string $methodName, array $arguments)
    {
        return $this
            ->getHookDispatcher()
            ->dispatch($methodName, $arguments[0] ?? []);
    }

    protected function getHookDispatcher(): HookDispatcher
    {
        if (!isset($this->hookDispatcher)) {
            if (!class_exists(HookDispatcher::class)) {
                require_once dirname(__FILE__) . '/vendor/autoload.php';
            }

            $this->hookDispatcher = new HookDispatcher($this);
        }

        return $this->hookDispatcher;
    }
}
