<?php

namespace Oksydan\Module\IsThemeCore\Core\StructuredData\Presenter;

class StructuredDataShopPresenter implements StructuredDataPresenterInterface
{
    private $presentedData = [];
    private $shopData;
    private $context;

    public function __construct(\Context $context)
    {
        $this->context = $context;
    }

    public function present($data): array
    {
        $this->shopData = $data;

        $this->presentShopData();

        return $this->presentedData;
    }

    private function presentShopData(): void
    {
        $this->presentedData['@context'] = 'http://schema.org';
        $this->presentedData['@type'] = 'Organization';
        $this->presentedData['name'] = $this->shopData['name'];
        $this->presentedData['url'] = $this->context->link->getPageLink('index');
        $this->presentedData['logo'] = [
            '@type' => 'ImageObject',
            'url' => $this->shopData['logo'],
        ];

        if ($this->shopData['phone']) {
            $this->presentedData['contactPoint'] = [
                '@type' => 'ContactPoint',
                'telephone' => $this->shopData['phone'],
                'contactType' => 'customer service',
            ];
        }

        $address = $this->shopData['address'];
        $postalCode = $address['postcode'];
        $city = $address['city'];
        $country = $address['country'];
        $addressRegion = $address['state'];
        $streetAddress = $address['address1'];

        if ($postalCode || $city || $country || $addressRegion || $streetAddress) {
            $this->presentedData['address'] = [
                '@type' => 'PostalAddress',
            ];

            if ($postalCode) {
                $this->presentedData['address']['postalCode'] = $postalCode;
            }
            if ($streetAddress) {
                $this->presentedData['address']['streetAddress'] = $streetAddress;
            }
            if ($country || $city) {
                $addressLocality = '';
                if ($city) {
                    $addressLocality = $city;
                }
                if ($country) {
                    $addressLocality .= ($addressLocality != '' ? ', ' : '') . $country;
                }

                $this->presentedData['address']['addressLocality'] = $addressLocality;
            }
        }
    }
}
