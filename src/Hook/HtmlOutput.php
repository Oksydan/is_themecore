<?php
/**
 * Copyright 2021-2022 InPost S.A.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the EUPL-1.2 or later.
 * You may not use this work except in compliance with the Licence.
 *
 * You may obtain a copy of the Licence at:
 * https://joinup.ec.europa.eu/software/page/eupl
 * It is also bundled with this package in the file LICENSE.txt
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the Licence is distributed on an AS IS basis,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the Licence for the specific language governing permissions
 * and limitations under the Licence.
 *
 * @author    InPost S.A.
 * @copyright 2021-2022 InPost S.A.
 * @license   https://joinup.ec.europa.eu/software/page/eupl
 */

namespace Oksydan\Module\IsThemeCore\Hook;
use Oksydan\Module\IsThemeCore\Form\Settings\GeneralConfiguration;

class HtmlOutput extends AbstractHook
{
    const HOOK_LIST = [
        'actionOutputHTMLBefore',
    ];

    const REL_LIST = [
        'preload',
        'preconnect',
    ];

    const PRELOAD_TYPES_TO_EARLY_HINT = [
        'image',
        'stylesheet',
        'font', //disabled for now causing higher LCP and weird FOUC
    ];

    public function hookActionOutputHTMLBefore(array $params) : void
    {
        $earlyHintsEnabled = \Configuration::get(GeneralConfiguration::THEMECORE_EARLY_HINTS);

        if (!$earlyHintsEnabled) {
            return;
        }

        $preConfig = libxml_use_internal_errors(true);
        $html = $params['html'];
        $doc = new \DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $links = $doc->getElementsByTagName('link');

        foreach ($links as $link) {
            $rel = $link->attributes->getNamedItem('rel')->nodeValue;

            if (in_array($rel, self::REL_LIST)) {
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

        libxml_use_internal_errors($preConfig);
    }

    private function handlePreloadFromNodeElement($nodeElement)
    {
        $preloadAs = $nodeElement->attributes->getNamedItem('as')->nodeValue;

        if (in_array($preloadAs, self::PRELOAD_TYPES_TO_EARLY_HINT)) {
            $url = $nodeElement->attributes->getNamedItem('href')->nodeValue;

            header("Link: $url; rel=preload; as=$preloadAs", false);
        }
    }

    private function handlePreconnectFromNodeElement($nodeElement)
    {
        $url = $nodeElement->attributes->getNamedItem('href')->nodeValue;

        header("Link: $url; rel=preconnect;", false);
    }
}
