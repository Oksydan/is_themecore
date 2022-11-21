<?php

declare(strict_types=1);

namespace Oksydan\Module\IsThemeCore\Form\Settings;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class GeneralFormDataProvider
 */
class GeneralFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $generalConfiguration;

    /**
     * @param DataConfigurationInterface $generalConfiguration
     */
    public function __construct(DataConfigurationInterface $generalConfiguration)
    {
        $this->generalConfiguration = $generalConfiguration;
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed> The form data as an associative array
     */
    public function getData(): array
    {
        return $this->generalConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string, mixed> $data
     *
     * @return array<int, array<string, mixed>> An array of errors messages if data can't persisted
     */
    public function setData(array $data): array
    {
        return $this->generalConfiguration->updateConfiguration($data);
    }
}
