<?php

namespace Oksydan\Module\IsThemeCore\Core\StructuredData\Provider;
use Oksydan\Module\IsThemeCore\Core\StructuredData\Provider\StructuredDataProviderInterface;

class StructuredDataProductProvider implements StructuredDataProviderInterface
{
  private $data = [];
  private $context;

  public function __construct(\Context $context)
  {
    $this->context = $context;
    $this->data = $this->context->controller->getTemplateVarProduct()->jsonSerialize();
    $this->provideProductCommentsDataIfModuleEnabled();
  }

  private function provideProductCommentsDataIfModuleEnabled() : void
  {
    $commentsData = [];

    if (\Module::isEnabled('productcomments')) {
      $productCommentRepository = $this->context->controller->getContainer()->get('product_comment_repository');
      $commentsModerate = (bool) \Configuration::get('PRODUCT_COMMENTS_MODERATE');
      $commentsNb = $productCommentRepository->getCommentsNumber($this->data['id'], $commentsModerate);

      if ($commentsNb > 0) {
        $averageGrade = $productCommentRepository->getAverageGrade($this->data['id'], $commentsModerate);
        $reviewsData = $productCommentRepository->paginate($this->data['id'], 1, 50, $commentsModerate); // get 50 reviews

        $commentsData = [
          'averageGrade' => $averageGrade,
          'commentsNb' => $commentsNb,
          'reviews' => $reviewsData,
        ];
      }
    }

    $this->data['productRating'] = $commentsData;
  }

  public function getData() : array
  {
    \Hook::exec('actionStructuredDataProductProvider',
      ['data' => &$this->data]
    );

    return $this->data;
  }
}
