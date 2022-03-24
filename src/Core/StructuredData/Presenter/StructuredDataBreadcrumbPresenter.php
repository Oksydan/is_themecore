<?php

namespace Oksydan\Module\IsThemeCore\Core\StructuredData\Presenter;
use Oksydan\Module\IsThemeCore\Core\StructuredData\Presenter\StructuredDataPresenterInterface;

class StructuredDataBreadcrumbPresenter implements StructuredDataPresenterInterface
{
  private $presentedData = [];
  private $breadcrumbData;

  public function present($breadcrumbData) : array
  {
    $this->breadcrumbData = $breadcrumbData;

    $this->presentBreadcrumbData();

    return $this->presentedData;
  }

  private function presentBreadcrumbData() : void
  {
    $breadcrumbs = $this->breadcrumbData['links'];

    if ($this->breadcrumbData['count'] > 1) {
      $this->presentedData['@context'] = 'http://schema.org';
      $this->presentedData['@type'] = 'BreadcrumbList';
      $this->presentedData['itemListElement'] = [];

      foreach ($breadcrumbs as $i => $breadcrumb) {
        $this->presentedData['itemListElement'][] = [
          '@type' => 'ListItem',
          'position' => $i + 1,
          'name' => $breadcrumb['title'],
          'item' => $breadcrumb['url'],
        ];
      }
    }
  }
}
