<?php

namespace Oksydan\Module\IsThemeCore\Core\StructuredData\Presenter;

class StructuredDataProductPresenter implements StructuredDataPresenterInterface
{
    private $presentedData = [];
    private $productData;
    private $context;

    public function __construct(\Context $context)
    {
        $this->context = $context;
    }

    public function present($data): array
    {
        $this->productData = $data;

        $this->getProductBasics();
        $this->getProductIdentifier();
        $this->getProductBrandData();
        $this->getProductReviewsData();
        $this->getProductOffers();

        return $this->presentedData;
    }

    private function getProductBasics(): void
    {
        $this->presentedData['@context'] = 'http://schema.org/';
        $this->presentedData['@type'] = 'Product';
        $this->presentedData['name'] = $this->productData['name'];
        $this->presentedData['category'] = $this->productData['category_name'];

        if (!empty($this->productData['description_short'])) {
            $this->presentedData['description'] = strip_tags($this->productData['description_short']);
        }

        if ($this->productData['default_image']) {
            $this->presentedData['image'] = $this->productData['default_image']['large']['url'];
        }

        if ($this->productData['reference']) {
            $this->presentedData['sku'] = $this->productData['reference'];
        }

        if (!empty($this->productData['weight']) && $this->productData['weight'] > 0) {
            $this->presentedData['weight'] = [
                '@context' => 'https://schema.org',
                '@type' => 'QuantitativeValue',
                'value' => $this->productData['weight'],
                'unitCode' => $this->productData['weight_unit'],
            ];
        }
    }

    private function getProductBrandData(): void
    {
        if (empty($this->productData['id_manufacturer'])) {
            return;
        }

        $productManufacturer = new \Manufacturer((int) $this->productData['id_manufacturer'], $this->context->language->id);

        if (!empty($productManufacturer->name)) {
            $this->presentedData['brand'] = [
                '@type' => 'Brand',
                'name' => $productManufacturer->name,
            ];
        }
    }

    private function getProductIdentifier(): void
    {
        if (!empty($this->productData['ean13'])) {
            $this->presentedData['gtin13'] = $this->productData['ean13'];
        } elseif (!empty($this->productData['upc'])) {
            $this->presentedData['gtin13'] = '0' . $this->productData['upc'];
        } elseif (!empty($this->productData['isbn'])) {
            $this->presentedData['isbn'] = $this->productData['isbn'];
        } elseif (!empty($this->productData['reference'])) {
            $this->presentedData['mpn'] = $this->productData['reference'];
        }
    }

    private function getProductOffers(): void
    {
        if (!$this->productData['show_price']) {
            return;
        }

        $this->presentedData['offers'] = [
            '@type' => 'Offer',
            'name' => $this->productData['name'],
            'price' => $this->productData['price_amount'],
            'url' => $this->productData['url'],
            'priceCurrency' => $this->context->currency->iso_code,
        ];

        if (count($this->productData['images']) > 0) {
            $images = [];

            foreach ($this->productData['images'] as $img) {
                $images[] = $img['large']['url'];
            }

            $this->presentedData['offers']['image'] = $images;
        }

        if ($this->productData['reference']) {
            $this->presentedData['offers']['sku'] = $this->productData['reference'];
        }

        $this->presentedData['offers']['availability'] = $this->productData['quantity'] > 0 || $this->productData['allow_oosp'] ? 'http://schema.org/InStock' : 'http://schema.org/OutOfStock';

        if ($this->productData['show_condition'] && isset($this->productData['condition'])) {
            $this->presentedData['offers']['itemCondition'] = $this->productData['condition']['schema_url'];
        }

        if ($this->productData['specific_prices'] && $this->productData['specific_prices']['to'] > (new \DateTime())->format('Y-m-d H:i:s')) {
            $date = new \DateTime($this->productData['specific_prices']['to']);
            $this->presentedData['offers']['priceValidUntil'] = $date->format('Y-m-d');
        }
    }

    private function getProductReviewsData(): void
    {
        if (empty($this->productData['productRating'])) {
            return;
        }

        $reviews = [];

        foreach ($this->productData['productRating']['reviews'] as $review) {
            $datePublished = new \DateTime($review['date_add']);

            $reviews[] = [
                '@type' => 'Review',
                'author' => [
                    '@type' => 'Person',
                    'name' => $review['customer_name'],
                ],
                'name' => $review['title'],
                'reviewBody' => $review['content'],
                'datePublished' => $datePublished->format(\DateTime::ATOM),
                'reviewRating' => [
                    '@type' => 'Rating',
                    'ratingValue' => $review['grade'],
                ],
            ];
        }

        $aggregateRating = [
            '@type' => 'AggregateRating',
            'ratingValue' => $this->productData['productRating']['averageGrade'],
            'ratingCount' => $this->productData['productRating']['commentsNb'],
            'reviewCount' => $this->productData['productRating']['commentsNb'],
        ];

        if ($reviews) {
            $this->presentedData['review'] = $reviews;
        }
        $this->presentedData['aggregateRating'] = $aggregateRating;
    }
}
