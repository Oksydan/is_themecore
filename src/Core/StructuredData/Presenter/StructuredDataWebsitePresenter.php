<?php

namespace Oksydan\Module\IsThemeCore\Core\StructuredData\Presenter;

class StructuredDataWebsitePresenter implements StructuredDataPresenterInterface
{
    private $presentedData = [];
    private $websiteData;
    private $context;

    public function __construct(\Context $context)
    {
        $this->context = $context;
    }

    public function present($data): array
    {
        $this->websiteData = $data;

        $this->presentShopData();

        return $this->presentedData;
    }

    private function presentShopData(): void
    {
        $this->presentedData['@context'] = 'http://schema.org';
        $this->presentedData['@type'] = 'WebSite';
        $this->presentedData['url'] = $this->context->link->getPageLink('index');
        $this->presentedData['image'] = [
            '@type' => 'ImageObject',
            'url' => $this->websiteData['logo'],
        ];
    }
}
