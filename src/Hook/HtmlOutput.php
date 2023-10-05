<?php

namespace Oksydan\Module\IsThemeCore\Hook;

use Oksydan\Module\IsThemeCore\Form\Settings\GeneralConfiguration;
use Oksydan\Module\IsThemeCore\Form\Settings\WebpConfiguration;

class HtmlOutput extends AbstractHook
{
    public const HOOK_LIST = [
        'actionOutputHTMLBefore',
    ];

    public const REL_LIST = [
        'preload',
        'preconnect',
    ];

    public const PRELOAD_TYPES_TO_EARLY_HINT = [
        'image',
        'stylesheet',
        // 'font', //disabled for now causing higher LCP and weird FOUC
    ];

    private $headers = [];

    public function hookActionOutputHTMLBefore(array $params): void
    {
        $earlyHintsEnabled = \Configuration::get(GeneralConfiguration::THEMECORE_EARLY_HINTS, false);
        $webpEnabled = \Configuration::get(WebpConfiguration::THEMECORE_WEBP_ENABLED, false);

        if (!$earlyHintsEnabled && !$webpEnabled) {
            return;
        }

        $preConfig = libxml_use_internal_errors(true);
        $html = $params['html'];
        $doc = new \DOMDocument();
        $doc->loadHTML(
            '<meta http-equiv="Content-Type" content="charset=utf-8">' . $html,
            LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        $links = $doc->getElementsByTagName('link');

        foreach ($links as $link) {
            $rel = $link->hasAttribute('rel') ? $link->attributes->getNamedItem('rel')->nodeValue : false;
            $as = $link->hasAttribute('as') ? $link->attributes->getNamedItem('as')->nodeValue : false;

            if ($webpEnabled && $rel === 'preload' && $as === 'image') {
                $newLink = $doc->createElement('link');
                $src = urldecode($link->attributes->getNamedItem('href')->nodeValue);

                $newLink->setAttribute('href', str_replace(['.png', '.jpg', '.jpeg'], '.webp', $src));

                foreach ($link->attributes as $attribute) {
                    if ($attribute->nodeName !== 'href') {
                        $newLink->setAttribute($attribute->nodeName, $attribute->nodeValue);
                    }
                }

                $link->parentNode->replaceChild($newLink, $link);
            }

            if ($earlyHintsEnabled && in_array($rel, self::REL_LIST)) {
                if (isset($newLink)) {
                    $link = $newLink;
                    unset($newLink);
                }

                switch ($rel) {
                    case 'preload':
                        $this->handlePreloadFromNodeElement($link);
                        break;
                    case 'preconnect':
                        $this->handlePreconnectFromNodeElement($link);
                        break;
                }
            }
        }

        if ($webpEnabled) {
            $content = $doc->saveHTML();
            $content = str_replace('<meta http-equiv="Content-Type" content="charset=utf-8">', '', $content);
            $params['html'] = $content;
        }

        if (!empty($this->headers)) {
            header('Link: ' . implode(', ', $this->headers));
        }

        libxml_use_internal_errors($preConfig);
    }

    private function handlePreloadFromNodeElement($nodeElement)
    {
        $preloadAs = $nodeElement->attributes->getNamedItem('as')->nodeValue;

        if (in_array($preloadAs, self::PRELOAD_TYPES_TO_EARLY_HINT)) {
            $url = $nodeElement->attributes->getNamedItem('href')->nodeValue;

            $this->headers[] = "<$url>; rel=preload; as=$preloadAs";
        }
    }

    private function handlePreconnectFromNodeElement($nodeElement)
    {
        $url = $nodeElement->attributes->getNamedItem('href')->nodeValue;

        $this->headers[] = "<$url>; rel=preconnect";
    }
}
