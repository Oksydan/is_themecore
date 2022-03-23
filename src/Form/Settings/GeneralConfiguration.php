<?php


declare(strict_types=1);

namespace Oksydan\Module\IsThemeCore\Form\Settings;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShopBundle\Service\Form\MultistoreCheckboxEnabler;

/**
 * Configuration is used to save data to configuration table and retrieve from it
 */
final class GeneralConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var string
     */
    public const THEMECORE_DISPLAY_LIST = 'THEMECORE_DISPLAY_LIST';

    /**
     * @var array<string, string>
     */
    private $fields = [
        'list_display_settings' => self::THEMECORE_DISPLAY_LIST,
    ];

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed>
     */
    public function getConfiguration(): array
    {
        $configurationValues = [];

        foreach ($this->fields as $field => $configurationKey) {
            $configurationValues[$field] = $this->configuration->get($configurationKey);
        }

        return $configurationValues;
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string, mixed> $configuration
     *
     * @return array<int, array<string, mixed>>
     */
    public function updateConfiguration(array $configuration): array
    {
        $errors = [];

        if (!$this->validateConfiguration($configuration)) {
            $errors[] = [
                'key' => 'Invalid configuration',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Warning',
            ];
        } else {
            $shopConstraint = $this->getShopConstraint();

            try {
                foreach ($this->fields as $field => $configurationKey) {
                    $this->updateConfigurationValue($configurationKey, $field, $configuration, $shopConstraint);
                }
            } catch (\Exception $exception) {
                $errors[] = [
                    'key' => $exception->getMessage(),
                    'parameters' => [],
                    'domain' => 'Admin.Notifications.Warning',
                ];
            }
        }

        return $errors;
    }

    /**
     * Ensure the parameters passed are valid.
     *
     * @param array<string, mixed> $configuration
     *
     * @return bool Returns true if no exception are thrown
     */
    public function validateConfiguration(array $configuration): bool
    {
        foreach ($this->fields as $field => $configurationKey) {
            $multistoreKey = MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX . $field;
            $this->fields[$multistoreKey] = '';
        }

        foreach ($configuration as $key => $value) {
            if (!key_exists($key, $this->fields)) {
                return false;
            }
        }

        return true;
    }
}
