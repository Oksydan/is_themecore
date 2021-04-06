<?php

if(!defined('_PS_VERSION_'))
    exit;

class ThemeAssets
{
    protected $themePath;
    public $pageName;
    public $context;

    public function __construct($pageName, $themeName, $context) {
      $this->pageName = $pageName;
      $this->themePath = 'themes/' . $themeName . '/assets/';
      $this->context = $context;
    }

    public function registerHooks()
    {
        $return = true;
        return $return;
    }

    public function setThemeAssets()
    {
      $styles = $this->getThemeCssByPage();
      $scripts = $this->getThemeJsByPage();


      foreach ($scripts as $script) {
        $this->context->controller->registerJavascript(
            'theme-' . $script['fileName'],
            $this->themePath . 'js/' . $script['fileName'],
            [
              'priority' => $script['priority'],
              'attribute' => $script['attribute'],
            ]
        );
      }

      foreach ($styles as $style) {
          $this->context->controller->registerStylesheet(
              'theme-' . $style['fileName'],
              $this->themePath . 'css/' . $style['fileName'],
              [
                'media' => $style['media'],
                'priority' => $style['priority'],
              ]
          );
      }
    }

    protected function getThemeJsByPage()
    {
        return $this->filterAssetsByPage($this->pageName, $this->getThemeJs());
    }

    protected function getThemeCssByPage()
    {
        return $this->filterAssetsByPage($this->pageName, $this->getThemeCss());
    }

    protected function filterAssetsByPage($page, $assets)
    {
        return array_filter($assets, function($asset) use ($page) {
            return in_array($page, $asset['allowedPages']) || empty($asset['allowedPages']);
        });
    }

    protected function getThemeJs()
    {
        return [
            [
                'fileName' => 'swipervendor.js',
                'allowedPages' => [],
                'attribute' => '',
                'priority' => 1
            ],
            [
                'fileName' => 'product.js',
                'allowedPages' => ['product'],
                'attribute' => '',
                'priority' => 200
            ],
            [
                'fileName' => 'checkout.js',
                'allowedPages' => ['order', 'cart', 'orderconfirmation'],
                'attribute' => '',
                'priority' => 200
            ],
            [
                'fileName' => 'listing.js',
                'allowedPages' => ['category', 'pricesdrop', 'newproducts', 'bestsales', 'manufacturer', 'search'],
                'attribute' => '',
                'priority' => 200
            ],
        ];
    }

    protected function getThemeCss()
    {
        return [
            [
                'fileName' => 'product.css',
                'allowedPages' => ['product'],
                'media' => 'all',
                'priority' => 200
            ],
            [
                'fileName' => 'checkout.css',
                'allowedPages' => ['order', 'cart', 'orderconfirmation'],
                'media' => 'all',
                'priority' => 200
            ],
            [
                'fileName' => 'listing.css',
                'allowedPages' => ['category', 'pricesdrop', 'newproducts', 'bestsales', 'manufacturer', 'search'],
                'media' => 'all',
                'priority' => 200
            ],

        ];
    }

    public function unregisterPsFacetedSearchAssets()
    {
        $this->context->controller->unregisterJavascript('facetedsearch_front');
        $this->context->controller->unregisterStylesheet('facetedsearch_front');

        $this->context->controller->unregisterJavascript('jquery-ui');
        $this->context->controller->unregisterStylesheet('jquery-ui');
    }
}
