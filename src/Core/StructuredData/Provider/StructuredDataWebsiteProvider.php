<?php

namespace Oksydan\Module\IsThemeCore\Core\StructuredData\Provider;

class StructuredDataWebsiteProvider implements StructuredDataProviderInterface
{
    private $data = [];

    public function __construct(\Context $context)
    {
        $this->data = $context->smarty->getTemplateVars('shop');
    }

    public function getData(): array
    {
        return $this->data;
    }
}
