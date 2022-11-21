<?php

declare(strict_types=1);

namespace Oksydan\Module\IsThemeCore\Form\Settings;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class WebpFormDataProvider
 */
class WebpFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $webpConfiguration;

    /**
     * @param DataConfigurationInterface $webpConfiguration
     */
    public function __construct(DataConfigurationInterface $webpConfiguration)
    {
        $this->webpConfiguration = $webpConfiguration;
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed> The form data as an associative array
     */
    public function getData(): array
    {
        return $this->webpConfiguration->getConfiguration();
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
        return $this->webpConfiguration->updateConfiguration($data);
    }
}
