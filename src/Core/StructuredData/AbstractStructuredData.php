<?php

namespace Oksydan\Module\IsThemeCore\Core\StructuredData;

use Oksydan\Module\IsThemeCore\Core\StructuredData\Presenter\StructuredDataPresenterInterface;
use Oksydan\Module\IsThemeCore\Core\StructuredData\Provider\StructuredDataProviderInterface;

abstract class AbstractStructuredData
{
    private StructuredDataProviderInterface $provider;
    private StructuredDataPresenterInterface $presenter;

    public function __construct(
        StructuredDataProviderInterface $provider,
        StructuredDataPresenterInterface $presenter
    ) {
        $this->provider = $provider;
        $this->presenter = $presenter;
    }

    /**
     * Return formatted json data
     *
     * @return string
     */
    public function getFormattedData(): string
    {
        $data = $this->provider->getData();

        $jsonData = $this->presenter->present($data);

        \Hook::exec('actionStructuredData' . ucfirst($this->getStructuredDataType()),
            [
                'jsonData' => &$jsonData,
                'rawData' => $data,
            ]
        );

        if (empty($jsonData)) {
            return '';
        } else {
            return json_encode($jsonData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    }
}
