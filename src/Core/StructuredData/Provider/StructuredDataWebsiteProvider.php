<?php

namespace Oksydan\Module\IsThemeCore\Core\StructuredData\Provider;

class StructuredDataWebsiteProvider implements StructuredDataProviderInterface
{
    protected \Context $context;

    public function __construct(\Context $context)
    {
        $this->context = $context;
    }

    public function getData(): array
    {
        return $this->context->smarty->getTemplateVars('shop');
    }
}
