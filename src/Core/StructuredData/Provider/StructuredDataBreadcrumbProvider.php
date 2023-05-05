<?php

namespace Oksydan\Module\IsThemeCore\Core\StructuredData\Provider;

class StructuredDataBreadcrumbProvider implements StructuredDataProviderInterface
{
    protected \Context $context;

    public function __construct(\Context $context)
    {
        $this->context = $context;
    }

    public function getData(): array
    {
        return $this->context->controller->getBreadcrumb();
    }
}
