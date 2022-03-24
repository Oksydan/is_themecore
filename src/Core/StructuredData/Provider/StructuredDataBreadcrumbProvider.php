<?php

namespace Oksydan\Module\IsThemeCore\Core\StructuredData\Provider;
use Oksydan\Module\IsThemeCore\Core\StructuredData\Provider\StructuredDataProviderInterface;

class StructuredDataBreadcrumbProvider implements StructuredDataProviderInterface
{
  private $data = [];

  public function __construct(\Context $context)
  {
    $this->data = $context->controller->getBreadcrumb();
  }

  public function getData() : array
  {
    return $this->data;
  }
}
