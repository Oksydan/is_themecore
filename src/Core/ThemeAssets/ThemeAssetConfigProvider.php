<?php

namespace Oksydan\Module\IsThemeCore\Core\ThemeAssets;

use Symfony\Component\Yaml\Yaml;

class ThemeAssetConfigProvider
{
    /**
     * @var bool
     */
    private $fileContentRead = false;
    /**
     * @var array
     */
    private $fileParsed = [];
    /**
     * @var string
     */
    public $themeAssetsFileDir;

    public function __construct($themeDir)
    {
        $this->themeAssetsFileDir = $themeDir . 'config/assets.yml';
    }

    public function getFileParsed(): array
    {
        if (!$this->fileContentRead) {
            if (file_exists($this->themeAssetsFileDir)) {
                $this->fileParsed = Yaml::parse(file_get_contents($this->themeAssetsFileDir));
            }

            $this->fileContentRead = true;
        }

        return $this->fileParsed;
    }

    public function getCssAssets(): array
    {
        $cssAssets = [];

        if (!empty($this->getFileParsed()['css'])) {
            $cssAssets = $this->getFileParsed()['css'];
        }

        return $cssAssets;
    }

    public function getJsAssets(): array
    {
        $jsAssets = [];

        if (!empty($this->getFileParsed()['js'])) {
            $jsAssets = $this->getFileParsed()['js'];
        }

        return $jsAssets;
    }
}
