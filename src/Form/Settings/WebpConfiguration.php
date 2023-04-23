<?php

declare(strict_types=1);

namespace Oksydan\Module\IsThemeCore\Form\Settings;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Configuration is used to save data to configuration table and retrieve from it
 */
final class WebpConfiguration implements DataConfigurationInterface
{
    private const CONFIGURATION_FIELDS = [
        'webp_enabled',
        'webp_quality',
        'webp_converter',
        'webp_sharpyuv',
    ];

    /**
     * @var string
     */
    public const THEMECORE_WEBP_ENABLED = 'THEMECORE_WEBP_ENABLED';
    public const THEMECORE_WEBP_QUALITY = 'THEMECORE_WEBP_QUALITY';
    public const THEMECORE_WEBP_CONVERTER = 'THEMECORE_WEBP_CONVERTER';
    public const THEMECORE_WEBP_SHARPYUV = 'THEMECORE_WEBP_SHARPYUV';

    /**
     * @var array<string, string>
     */
    private $fields = [
        'webp_enabled' => self::THEMECORE_WEBP_ENABLED,
        'webp_quality' => self::THEMECORE_WEBP_QUALITY,
        'webp_converter' => self::THEMECORE_WEBP_CONVERTER,
        'webp_sharpyuv' => self::THEMECORE_WEBP_SHARPYUV,
    ];

    /**
     * @var Configuration
     */
    protected $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

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
            try {
                foreach ($this->fields as $field => $configurationKey) {
                    $this->configuration->set($configurationKey, $configuration[$field]);
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
        foreach ($configuration as $key => $value) {
            if (!key_exists($key, $this->fields)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('webp_enabled', 'bool')
            ->setAllowedTypes('webp_quality', 'string')
            ->setAllowedTypes('webp_converter', 'string')
            ->setAllowedTypes('webp_sharpyuv', 'bool');
    }
}
