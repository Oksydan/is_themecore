<?php

namespace Oksydan\Module\IsThemeCore\Core\StructuredData;

class ProductStructuredData extends AbstractStructuredData implements StructuredDataInterface
{
    public function getStructuredDataType(): string
    {
        return 'product';
    }
}
