<?php

namespace Oksydan\Module\IsThemeCore\Core\StructuredData\Provider;

class StructuredDataProductProvider implements StructuredDataProviderInterface
{
    private array $data = [];
    private \Context $context;

    public function __construct(\Context $context)
    {
        $this->context = $context;
    }

    private function provideProductCommentsDataIfModuleEnabled(): void
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

    public function getProductData(): void
    {
        $this->data = $this->context->controller->getTemplateVarProduct()->jsonSerialize();
    }

    public function getData(): array
    {
        $this->getProductData();
        $this->provideProductCommentsDataIfModuleEnabled();

        return $this->data;
    }
}
