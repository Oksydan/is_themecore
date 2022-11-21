<?php

namespace Oksydan\Module\IsThemeCore\Core\Webp;

use WebPConvert\WebPConvert;

class WebpGenerator
{
    protected $fileFinder;
    protected $destinationFile = '';
    protected $converter = false;
    protected $debugEnabled = false;
    protected $sharpYuv = false;
    protected $quality = 90;

    public function __construct(RelatedImageFileFinder $fileFinder)
    {
        $this->fileFinder = $fileFinder;
    }

    public function setQuality($quality)
    {
        $this->quality = $quality;

        return $this;
    }

    public function getQuality(): int
    {
        return $this->quality;
    }

    public function setConverter($converter)
    {
        $this->converter = $converter;

        return $this;
    }

    public function getConverter(): string
    {
        return $this->converter;
    }

    public function setSharpYuv($sharpYuv)
    {
        $this->sharpYuv = $sharpYuv;

        return $this;
    }

    public function getSharpYuv(): bool
    {
        return $this->sharpYuv;
    }

    public function setDebugEnabled($debugEnabled)
    {
        $this->debugEnabled = $debugEnabled;

        return $this;
    }

    public function getDebugEnabled(): bool
    {
        return $this->debugEnabled;
    }

    public function setDestinationFile($destinationFile)
    {
        $this->destinationFile = $destinationFile;

        return $this;
    }

    public function getDestinationFile(): string
    {
        return $this->destinationFile;
    }

    public function findRelatedFile()
    {
        return $this->fileFinder->findFile($this->getDestinationFile());
    }

    public function convertAndServe()
    {
        $sourceFile = $this->findRelatedFile();

        WebPConvert::serveConverted($sourceFile, $this->destinationFile, [
            'fail' => 'original',
            'show-report' => $this->getDebugEnabled(),

            'serve-image' => [
                'headers' => [
                    'cache-control' => true,
                    'vary-accept' => true,
                    // other headers can be toggled...
                ],
                'cache-control-header' => 'max-age=2',
            ],
            'convert' => [
                'stack-converters' => [$this->getConverter()],
                'quality' => $this->getQuality(),
                'encoding' => 'auto',
                'sharp-yuv' => $this->getSharpYuv(),
            ],
        ]);
    }
}
