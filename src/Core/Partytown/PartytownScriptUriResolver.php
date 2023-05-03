<?php

namespace Oksydan\Module\IsThemeCore\Core\Partytown;

class PartytownScriptUriResolver
{
    private \Context $context;

    const PUBLIC_PARTYTOWN_PATH = '~partytown/';

    public function __construct(
        \Context $context
    ) {
        $this->context = $context;
    }

    public function getScriptUri(): string
    {
        return $this->context->shop->physical_uri . self::PUBLIC_PARTYTOWN_PATH;
    }
}
