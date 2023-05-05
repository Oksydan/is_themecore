<?php

namespace Oksydan\Module\IsThemeCore\Core\StructuredData;

class ShopStructuredData extends AbstractStructuredData implements StructuredDataInterface
{
    public function getStructuredDataType(): string
    {
        return 'shop';
    }
}
