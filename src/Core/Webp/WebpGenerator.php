<?php

namespace Oksydan\Module\IsThemeCore\Core\Webp;
use Oksydan\Module\IsThemeCore\Core\Webp\RelatedImageFileFinder;
use WebPConvert\WebPConvert;

class WebpGenerator
{
  protected $fileFinder;
  protected $destinationFile = '';
  protected $debugEnabled = false;
  protected $quality = 90;

  public function __construct(RelatedImageFileFinder $fileFinder)
  {
    $this->fileFinder = $fileFinder;
  }

  public function setDebugEnabled($debugEnabled)
  {
    $this->debugEnabled = $debugEnabled;
    return $this;
  }

  public function getDebugEnabled()
  {
    return $this->debugEnabled;
  }

  public function setDestinationFile($destinationFile)
  {
    $this->destinationFile = $destinationFile;
    return $this;
  }

  public function getDestinationFile()
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
      'show-report' => $this->debugEnabled,

      'serve-image' => [
          'headers' => [
              'cache-control' => true,
              'vary-accept' => true,
              // other headers can be toggled...
          ],
          'cache-control-header' => 'max-age=2',
      ],
      'convert' => [
        // 'stack-converters' => ['imagick', 'gmagick', 'imagemagick', 'vips', 'graphicsmagick', 'wpc', 'gd'],
        'quality' => $this->quality,
        'encoding' => 'auto',
        'sharp-yuv' => true,
      ]
    ]);
  }
}
