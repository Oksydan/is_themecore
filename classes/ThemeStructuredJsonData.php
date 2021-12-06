<?php

class ThemeStructuredJsonData {
  public $context;
  protected $structuredJsonData;
  public $templateVarsPage;
  public $templateVarsShop;
  public $breadcrumbs;

  public function __construct() {
    $this->structuredJsonData = [];
    $this->context = Context::getContext();
    $this->templateVarsPage = $this->context->smarty->getTemplateVars('page');
    $this->templateVarsShop = $this->context->smarty->getTemplateVars('shop');
    $this->breadcrumbs = $this->context->controller->getBreadcrumb();
    $this->getData();
  }

  public function getData() {
    if ($this->context->controller->getPageName() === 'product') {
      $this->getProductData();
    }

    $this->getWebsiteData();
    $this->getShopData();
    $this->getWebsiteBreadcrumb();
  }

  public function getJsonData() {
    return ($this->structuredJsonData);
  }

  private function addStructuredData($data) {
    $this->structuredJsonData[] = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
  }

  private function getProductBarCode($product)
  {
    $productBarCode = [];

    if ($product['ean13']) {
      $productBarCode['gtin13'] = $product['ean13'];
    } elseif ($product['upc']) {
      $productBarCode['gtin13'] = '0' . $product['upc'];
    } elseif ($product['isbn']) {
      $productBarCode['isbn'] = $product['isbn'];
    } elseif ($product['reference']) {
      $productBarCode['mpn'] = $product['reference'];
    }

    return $productBarCode;
  }

  private function getWebsiteBreadcrumb()
  {
    $jsonData = [];
    $breadcrumbs = $this->breadcrumbs['links'];

    if ($this->breadcrumbs['count'] > 1) {
      $jsonData['@context'] = 'http://schema.org';
      $jsonData['@type'] = 'BreadcrumbList';
      $jsonData['itemListElement'] = [];

      foreach ($breadcrumbs as $i => $breadcrumb) {
        $jsonData['itemListElement'][] = [
          '@type' => 'ListItem',
          'position' => $i + 1,
          'name' => $breadcrumb['title'],
          'item' => $breadcrumb['url'],
        ];
      }
    }

    $this->addStructuredData($jsonData);
  }

  private function getWebsiteData()
  {
    if($this->context->controller->getPageName() !== 'index') {
      return;
    }

    $jsonData = [];

    $jsonData['@context'] = 'http://schema.org';
    $jsonData['@type'] = 'WebSite';
    $jsonData['url'] = $this->context->link->getPageLink('index');
    $jsonData['image'] = [
      '@type' => 'ImageObject',
      'url' => $this->context->link->getPageLink('index') . $this->templateVarsShop['logo']
    ];

    $this->addStructuredData($jsonData);
  }

  private function getShopData()
  {
    $jsonData = [];

    $jsonData['@context'] = 'http://schema.org';
    $jsonData['@type'] = 'Organization';
    $jsonData['name'] = $this->templateVarsShop['name'];
    $jsonData['url'] = $this->context->link->getPageLink('index');
    $jsonData['logo'] = [
      '@type' => 'ImageObject',
      'image' => $this->context->link->getPageLink('index') . $this->templateVarsShop['logo']
    ];

    if($this->templateVarsShop['phone']) {
      $jsonData['contactPoint'] = [
        '@type' => 'ContactPoint',
        'telephone' => $this->templateVarsShop['phone'],
        'contactType' => 'customer service'
      ];
    }

    $address = $this->templateVarsShop['address'];
    $postalCode = $address['postcode'];
    $city = $address['city'];
    $country = $address['country'];
    $addressRegion = $address['state'];
    $streetAddress = $address['address1'];

    if ($postalCode || $city || $country || $addressRegion || $streetAddress) {
      $jsonData['address'] = [
        '@type' => 'PostalAddress'
      ];

      if ($postalCode) {
        $jsonData['address']['postalCode'] = $postalCode;
      }
      if ($streetAddress) {
        $jsonData['address']['streetAddress'] = $streetAddress;
      }
      if ($country || $city) {
        $addressLocality = '';
        if($city) {
          $addressLocality = $city;
        }
        if($country) {
          $addressLocality .= ($addressLocality != '' ? ', ' : '') . $country;
        }

        $jsonData['address']['addressLocality'] = $addressLocality;
      }
    }

    $this->addStructuredData($jsonData);
  }

  private function getProductCommentsData($product)
  {
    $commentsData = [];

    if (Module::isEnabled('productcomments')) {
      $productCommentRepository = $this->context->controller->getContainer()->get('product_comment_repository');

      $averageGrade = $productCommentRepository->getAverageGrade($product->id, (bool) Configuration::get('PRODUCT_COMMENTS_MODERATE'));
      $commentsNb = $productCommentRepository->getCommentsNumber($product->id, (bool) Configuration::get('PRODUCT_COMMENTS_MODERATE'));

      if ($commentsNb > 0) {
        $commentsData = [
          'aggregateRating' => [
            '@type' => 'AggregateRating',
            'ratingValue' => $averageGrade,
            'ratingCount' => $commentsNb,
            'reviewCount' => $commentsNb
          ]
        ];
      }
    }

    return $commentsData;
  }

  private function getProductData()
  {
    $product = $this->context->controller->getTemplateVarProduct();

    $barCode = $this->getProductBarCode($product);

    $jsonData = [];

    $jsonData['@context']= 'http://schema.org/';
    $jsonData['@type']= 'Product';
    $jsonData['name'] = $product['name'];

    if (isset($product['description_short'])) {
      $jsonData['description'] = strip_tags($product['description_short']);
    }

    $jsonData['category']= $product['category_name'];

    if ($product['default_image']) {
      $jsonData['image'] = $product['default_image']['bySize']['home_default']['url'];
    }

    if ($product['reference']) {
      $jsonData['sku'] = $product['reference'];
    }

    $jsonData = array_merge($jsonData, $barCode);

    $productManufacturer = new Manufacturer((int) $product['id_manufacturer'], $this->context->language->id);

    if (!empty($productManufacturer->name)) {
      $jsonData['brand'] = [
        '@type' => 'Brand',
        'name' => $productManufacturer->name
      ];
    } else {
      $jsonData['brand'] = [
        '@type' => 'Brand',
        'name' => $this->templateVarsShop['name']
      ];
    }

    if (isset($product['weight']) && $product['weight'] > 0) {
      $jsonData['weight'] = [
        '@context' => 'https://schema.org',
        '@type' => 'QuantitativeValue',
        'value' => $product['weight'],
        'unitCode' => $product['weight_unit'],
      ];
    }

    $commentsData = $this->getProductCommentsData($product);

    if (!empty($commentsData)) {
      $jsonData = array_merge($jsonData, $commentsData);
    }

    if ($product['show_price']) {
      $jsonData['offers'] = [
        '@type' => 'Offer',
        'name' => $product['name'],
        'price' => $product['price_amount'],
        'url' => $product['url'],
        'priceCurrency' => $this->context->currency->iso_code
      ];

      if (count($product['images']) > 0) {
        $images = [];

        foreach ($product['images'] as $img) {
          $images[] = $img['large']['url'];
        }

        $jsonData['offers'] = array_merge($jsonData['offers'], [
          'image' => $images
        ]);
      }

      if ($product['reference']) {
        $jsonData['offers'] = array_merge($jsonData['offers'], [
          'sku' => $product['reference']
        ]);
      }

      $jsonData['offers'] = array_merge($jsonData['offers'], $barCode);

      $conditions = [
        'new' => 'http://schema.org/NewCondition',
        'used' => 'http://schema.org/UsedCondition',
        'refurbished' => 'http://schema.org/RefurbishedCondition',
      ];

      if ($product['show_condition'] && $product['condition']) {
        $jsonData['offers'] = array_merge($jsonData['offers'], [
          'itemCondition' => $conditions[$product['condition']]
        ]);
      }

      $jsonData['offers'] = array_merge($jsonData['offers'], [
        'availability' => $product['quantity'] > 0 || $product['allow_oops'] ? 'http://schema.org/InStock' : 'http://schema.org/OutOfStock'
      ]);

      $jsonData['offers'] = array_merge($jsonData['offers'], [
        'seller' => [
          '@type' => 'Organization',
          'name' => $this->templateVarsShop['name']
        ]
      ]);

      if($product['specific_prices'] && $product['specific_prices']['to'] > date("Y-m-d H:i:s")) {
        $date = new DateTime($product['specific_prices']['to']);
        $jsonData['offers'] = array_merge($jsonData['offers'], [
          'priceValidUntil' => $date->format('Y-m-d')
        ]);
      }
    }

    $this->addStructuredData($jsonData);
  }

  public function getListingData($listing)
  {
    $jsonData = [];
    $products = [];

    if (isset($listing['products']) && $listing['products']) {
      $products = $listing['products'];
    }

    if ($products) {
      $jsonData['@context'] = 'http://schema.org';
      $jsonData['@type'] = 'ItemList';
      $jsonData['itemListElement'] = [];

      foreach ($products as $index => $product) {
        $jsonData['itemListElement'][] = [
          '@type' => 'ListItem',
          'position' => intval($index) + 1,
          'name' => $product['name'],
          'url' => $product['url']
        ];
      }
    }

    $this->addStructuredData($jsonData);
  }

}
