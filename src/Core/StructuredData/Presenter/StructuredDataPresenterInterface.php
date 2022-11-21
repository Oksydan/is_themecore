<?php

namespace Oksydan\Module\IsThemeCore\Core\StructuredData\Presenter;

interface StructuredDataPresenterInterface
{
    /**
     * Return formatted data
     *
     * @param array $data Data from provider
     *
     * @return array
     */
    public function present($data);
}
