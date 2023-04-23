<?php

namespace Oksydan\Module\IsThemeCore\Core\Htaccess;

class HtaccessGenerator
{
    private $module;
    private $domains = [];
    private $medias = [];

    protected $moduleWebpGeneratorFile;
    protected $mediaDomains = null;
    protected $tempContent = '';
    protected $contentBefore = '';
    protected $contentAfter = '';

    protected $wrapperBlockComments = [
        'COMMENT_START' => '~~start-is_themecore~~',
        'COMMENT_END' => '~~end-is_themecore~~',
    ];

    public function __construct(\Is_themecore $module)
    {
        $this->domains = \Tools::getDomains();
        $this->module = $module;
        $this->moduleWebpGeneratorFile = "%{ENV:REWRITEBASE}modules/{$this->module->name}/webp.php";
    }

    public function generate($addRewrite = true): void
    {
        $htaccessFile = $this->getHtaccessFilePath();

        if (file_exists($htaccessFile)) {
            $content = \Tools::file_get_contents($htaccessFile);

            if (preg_match('#^(.*)\# ' . $this->wrapperBlockComments['COMMENT_START'] . '.*\# ' . $this->wrapperBlockComments['COMMENT_END'] . '[^\n]*(.*)$#s', $content, $match)) {
                $this->contentBefore = $match[1];
                $this->contentAfter = $match[2];
            } else {
                $this->contentAfter = $content;
            }
        }

        if ($addRewrite) {
            $this->generateHtaccessHeader();

            $this->write('<IfModule mod_rewrite.c>');
            $this->write('RewriteEngine On');
            $this->writeNl();
            $this->generateImagesRewrites();
            $this->write('</IfModule>');

            $this->write("# {$this->wrapperBlockComments['COMMENT_END']} Do not remove this comment");
        }

        $this->write($this->contentAfter);
    }

    public function writeFile(): bool
    {
        $htaccessFile = $this->getHtaccessFilePath();

        if (!$writeToFile = @fopen($htaccessFile, 'wb')) {
            return false;
        }

        if (!fwrite($writeToFile, $this->tempContent)) {
            return false;
        }

        fclose($writeToFile);

        return true;
    }

    protected function getMediaDomains(): string
    {
        if ($this->mediaDomains === null) {
            if (\Configuration::getMultiShopValues('PS_MEDIA_SERVER_1')
                && \Configuration::getMultiShopValues('PS_MEDIA_SERVER_2')
                && \Configuration::getMultiShopValues('PS_MEDIA_SERVER_3')
            ) {
                $this->medias = [
                    \Configuration::getMultiShopValues('PS_MEDIA_SERVER_1'),
                    \Configuration::getMultiShopValues('PS_MEDIA_SERVER_2'),
                    \Configuration::getMultiShopValues('PS_MEDIA_SERVER_3'),
                ];
            }

            $this->mediaDomains = '';

            foreach ($this->medias as $media) {
                foreach ($media as $mediaUrl) {
                    if ($mediaUrl) {
                        $this->mediaDomains .= 'RewriteCond %{HTTP_HOST} ^' . $mediaUrl . '$ [OR]' . PHP_EOL;
                    }
                }
            }
        }

        return $this->mediaDomains;
    }

    protected function getDomainRewriteCond($domain): string
    {
        return "RewriteCond %{HTTP_HOST} ^$domain$";
    }

    protected function generateImagesRewrites(): void
    {
        foreach ($this->domains as $domain => $uri) {
            $this->generateProductImagesRewrite($domain);
            $this->generateCategoryImagesRewrite($domain);
            $this->generateOtherImagesRewrite($domain);
        }
    }

    protected function writeMediaDomainsCondition()
    {
        $mediaDomains = $this->getMediaDomains();

        if ($mediaDomains) {
            $this->write($mediaDomains, false);
        }
    }

    protected function generateProductImagesRewrite($domain): void
    {
        $domainRewriteCond = $this->getDomainRewriteCond($domain);

        for ($i = 1; $i <= 7; ++$i) {
            $imgPath = $imgName = '';
            for ($j = 1; $j <= $i; ++$j) {
                $imgPath .= '$' . $j . '/';
                $imgName .= '$' . $j;
            }
            $imgName .= '$' . $j;

            // WEBP FILE EXISTS
            $this->writeMediaDomainsCondition();
            $this->write($domainRewriteCond);
            $this->write('RewriteCond %{DOCUMENT_ROOT}/img/p/' . $imgPath . $imgName . '$' . ($j + 1) . '.webp -f');
            $this->write('RewriteRule ^' . str_repeat('([0-9])', $i) . '(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.webp$ %{ENV:REWRITEBASE}img/p/' . $imgPath . $imgName . '$' . ($j + 1) . '.webp [L]');
            $this->writeNl();

            // WEBP FILE NOT EXISTS
            $this->writeMediaDomainsCondition();
            $this->write($domainRewriteCond);
            $this->write('RewriteCond %{DOCUMENT_ROOT}/img/p/' . $imgPath . $imgName . '$' . ($j + 1) . '.webp !-f');
            $this->write('RewriteRule ^' . str_repeat('([0-9])', $i) . '(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/.+\.webp$ ' . $this->moduleWebpGeneratorFile . '?source=%{DOCUMENT_ROOT}/img/p/' . $imgPath . $imgName . '$' . ($j + 1) . '.webp [NC,L]');
            $this->writeNl();
        }
    }

    protected function generateCategoryImagesRewrite($domain): void
    {
        $domainRewriteCond = $this->getDomainRewriteCond($domain);

        // WEBP FILE EXISTS
        $this->writeMediaDomainsCondition();
        $this->write($domainRewriteCond);
        $this->write('RewriteCond %{DOCUMENT_ROOT}/img/c/$1$2.webp -f');
        $this->write('RewriteRule ^c/([0-9]+)(\-[\.*_a-zA-Z0-9-]*)(-[0-9]+)?/.+\.webp$ %{ENV:REWRITEBASE}img/c/$1$2.webp [L]');
        $this->writeNl();

        $this->writeMediaDomainsCondition();
        $this->write($domainRewriteCond);
        $this->write('RewriteCond %{DOCUMENT_ROOT}/img/c/$1$2$3.webp -f');
        $this->write('RewriteRule ^c/([0-9]+)(\-[\.*_a-zA-Z0-9-]*)(-[0-9]+)?/.+\.webp$ %{ENV:REWRITEBASE}img/c/$1$2$3.webp [L]');
        $this->writeNl();

        // WEBP FILE NOT EXISTS
        $this->writeMediaDomainsCondition();
        $this->write($domainRewriteCond);
        $this->write('RewriteCond %{DOCUMENT_ROOT}/img/c/$1$2.webp !-f');
        $this->write('RewriteRule ^c/([0-9]+)(\-[\.*_a-zA-Z0-9-]*)(-[0-9]+)?/.+\.webp$ ' . $this->moduleWebpGeneratorFile . '?source=%{DOCUMENT_ROOT}/img/c/$1$2.webp [NC,L]');
        $this->writeNl();

        $this->writeMediaDomainsCondition();
        $this->write($domainRewriteCond);
        $this->write('RewriteCond %{DOCUMENT_ROOT}/img/c/$1$2$3.webp !-f');
        $this->write('RewriteRule ^c/([0-9]+)(\-[\.*_a-zA-Z0-9-]*)(-[0-9]+)?/.+\.webp$ ' . $this->moduleWebpGeneratorFile . '?source=%{DOCUMENT_ROOT}/img/c/$1$2$3.webp [NC,L]');
        $this->writeNl();
    }

    protected function generateOtherImagesRewrite($domain): void
    {
        $domainRewriteCond = $this->getDomainRewriteCond($domain);

        // WEBP FILE NOT EXISTS
        $this->writeMediaDomainsCondition();
        $this->write($domainRewriteCond);
        $this->write('RewriteCond %{REQUEST_FILENAME} !-f');
        $this->write('RewriteRule ^(.*)\.webp$ ' . $this->moduleWebpGeneratorFile . '?source=%{DOCUMENT_ROOT}/$1.webp [NC,L]');
        $this->writeNl();
    }

    protected function generateHtaccessHeader(): void
    {
        $this->write("# {$this->wrapperBlockComments['COMMENT_START']} Do not remove this comment");
        $this->write('# Allow webp files to be sent by Apache 2.2');
        $this->write('<IfModule !mod_authz_core.c>');
        $this->write('<Files ~ "\\.(webp)$">', true, 1);
        $this->write('Allow from all', true, 2);
        $this->write('</Files>', true, 1);
        $this->write('</IfModule>');
        $this->writeNl();

        $this->write('# Allow webp files to be sent by Apache 2.4');
        $this->write('<IfModule mod_authz_core.c>');
        $this->write('<Files ~ "\\.(webp)$">', true, 1);
        $this->write('Require all granted', true, 2);
        $this->write('allow from all', true, 2);
        $this->write('</Files>', true, 1);
        $this->write('</IfModule>');
        $this->writeNl();
    }

    protected function write($line, $addEOL = true, $tabs = 0): void
    {
        $this->tempContent .= str_repeat("\t", $tabs) . $line . ($addEOL ? PHP_EOL : '');
    }

    protected function writeNl(): void
    {
        $this->write('');
    }

    protected function getHtaccessFilePath(): string
    {
        return _PS_ROOT_DIR_ . '/.htaccess';
    }
}
