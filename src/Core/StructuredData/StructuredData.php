<?php

namespace Oksydan\Module\IsThemeCore\Core\StructuredData;

class StructuredData
{
  private $provider;
  private $presenter;

  public function __construct($provider, $presenter)
  {
    $this->provider = $provider;
    $this->presenter = $presenter;
  }

  /**
   * Return formatted json data
   *
   * @return string
   */
  public function getFormattedData() : string
  {
    $jsonData = $this->presenter->present($this->provider->getData());

    if (empty($jsonData)) {
      return '';
    } else {
      return json_encode($jsonData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
  }
}
