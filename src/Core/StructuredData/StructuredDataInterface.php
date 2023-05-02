<?php

namespace Oksydan\Module\IsThemeCore\Core\StructuredData;

interface StructuredDataInterface
{
    /**
     * Return formatted json data
     *
     * @return string
     */
    public function getFormattedData(): string;

    public function getStructuredDataType(): string;
}
