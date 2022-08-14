<?php

require_once __DIR__ . '/../../config/config.inc.php';
require_once __DIR__ . '/is_themecore.php';

use Oksydan\Module\IsThemeCore\Core\Webp\WebpGenerator;
use Oksydan\Module\IsThemeCore\Core\Webp\RelatedImageFileFinder;

$webpGenerator = new WebpGenerator((new RelatedImageFileFinder()));

$webpGenerator->setDestinationFile($_GET['source']);
// $webpGenerator->setDebugEnabled(true);
$webpGenerator->convertAndServe();
die();
