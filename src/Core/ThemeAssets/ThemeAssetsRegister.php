<?php

namespace Oksydan\Module\IsThemeCore\Core\ThemeAssets;


class ThemeAssetsRegister {

  /**
   * @var ThemeAssetConfigProvider
   */
  private $assetsDataProvider;
  /**
   * @var string
   */
  private $currentPageName;
  /**
   * @var string
   */
  private $themeName;
  /**
   * @var array
   */
  private $cssAssets = [];
  /**
   * @var array
   */
  private $jsAssets = [];

  public function __construct(ThemeAssetConfigProvider $assetsDataProvider, \Context $context)
  {
    $this->assetsDataProvider = $assetsDataProvider;
    $this->context = $context;
    $this->themeName = $this->context->shop->theme->getName();
    $this->currentPageName = $this->context->controller->getPageName();
    $this->themePath = 'themes/' . $this->themeName . '/assets/';
    $this->cssAssets = $assetsDataProvider->getCssAssets();
    $this->jsAssets = $assetsDataProvider->getJsAssets();
  }

  private function getFilteredCssAssetsByPage() : array
  {
    return $this->filterAssetsArrayByPage($this->cssAssets);
  }

  private function getFilteredJsAssetsByPage() : array
  {
    return $this->filterAssetsArrayByPage($this->jsAssets);
  }

  private function filterAssetsArrayByPage($assetsArray) : array
  {
    $pageName = $this->currentPageName;

    return array_filter($assetsArray, function($asset) use ($pageName) {
      if (empty($asset['include'])) {
        return true;
      }

      if (in_array($pageName, $asset['include'])) {
        return true;
      }

      foreach ($asset['include'] as $matchType) {
        $regex = str_replace(
          ['\*'],
          ['.*', '.'],
          preg_quote($matchType)
        );

        if (preg_match('/^' . $regex . '$/is', $pageName)) {
          return true;
        }
      }

      return false;
    });
  }

  public function registerThemeAssets() : void
  {
    $this->registerJsAssets();
    $this->registerCssAssets();
  }

  public function registerJsAssets() : void
  {
    $assetsToRegister = $this->getFilteredJsAssetsByPage();

    $default_params = [
      'position' => \AbstractAssetManager::DEFAULT_JS_POSITION,
      'priority' => \AbstractAssetManager::DEFAULT_PRIORITY,
      'inline' => false,
      'attributes' => null,
    ];

    foreach($assetsToRegister as $id => $asset) {
      $params = array_merge($default_params, $asset);

      $this->context->controller->registerJavascript(
        'theme-' . $id,
        $this->themePath . 'js/' . $asset['fileName'],
        [
          'position' => $params['position'],
          'priority' => $params['priority'],
          'inline' => $params['inline'],
          'attributes' => $params['attributes'],
          'server' => 'local',
        ]
      );
    }
  }

  public function registerCssAssets() : void
  {
    $assetsToRegister = $this->getFilteredCssAssetsByPage();

    $default_params = [
      'media' => \AbstractAssetManager::DEFAULT_MEDIA,
      'priority' => \AbstractAssetManager::DEFAULT_PRIORITY,
      'inline' => false,
    ];

    foreach($assetsToRegister as $id => $asset) {
      $params = array_merge($default_params, $asset);

      $this->context->controller->registerStylesheet(
        'theme-' . $id,
        $this->themePath . 'css/' . $asset['fileName'],
        [
          'media' => $params['media'],
          'priority' => $params['priority'],
          'server' => 'local',
        ]
      );
    }
  }

}
