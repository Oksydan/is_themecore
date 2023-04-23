<?php

require_once __DIR__ . '/../../config/config.inc.php';
require_once __DIR__ . '/is_themecore.php';

use Oksydan\Module\IsThemeCore\Core\Webp\RelatedImageFileFinder;
use Oksydan\Module\IsThemeCore\Core\Webp\WebpGenerator;
use Oksydan\Module\IsThemeCore\Form\Settings\WebpConfiguration;

$webpGenerator = new WebpGenerator(new RelatedImageFileFinder());

$webpGenerator->setDestinationFile($_GET['source']);
$webpGenerator->setQuality((int) Configuration::get(WebpConfiguration::THEMECORE_WEBP_QUALITY));
$webpGenerator->setConverter(Configuration::get(WebpConfiguration::THEMECORE_WEBP_CONVERTER));
$webpGenerator->setSharpYuv((bool) Configuration::get(WebpConfiguration::THEMECORE_WEBP_SHARPYUV));
$webpGenerator->convertAndServe();
exit;
