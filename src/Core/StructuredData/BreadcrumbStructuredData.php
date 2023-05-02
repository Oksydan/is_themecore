<?php

namespace Oksydan\Module\IsThemeCore\Core\StructuredData;

class BreadcrumbStructuredData extends AbstractStructuredData implements StructuredDataInterface
{
    public function getStructuredDataType(): string
    {
        return 'breadcrumb';
    }
}
