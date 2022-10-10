<?php

namespace Oksydan\Module\IsThemeCore\Core\Webp;

use WebPConvert\Convert\ConverterFactory;
use WebPConvert\Convert\Exceptions\ConversionFailed\InvalidInput\ConverterNotFoundException;
use WebPConvert\Convert\Exceptions\ConversionFailedException;

class WebpConvertLibraries
{
    protected $converters = [
        'cwebp' => ['label' => 'Cwebp binary'],
        'vips' => ['label' => 'Vips PHP extension'],
        'imagick' => ['label' => 'Imagick PHP extension'],
        'gmagick' => ['label' => 'Gmagick PHP extension'],
        'imagemagick' => ['label' => 'Imagemagick binary'],
        'graphicsmagick' => ['label' => 'Graphicsmagick binary (gm)'],
        'gd' => ['label' => 'Gd PHP extension'],
        // NOT SUPPORTED
        // 'ewww' => ['label' => 'EWWW cloud service'],
    ];

    protected $exampleImgFile = _PS_MODULE_DIR_ . 'is_themecore/views/img/example.jpg';
    protected $exampleImgFileDesc = _PS_MODULE_DIR_ . 'is_themecore/views/img/example.webp';

    public function getConvertersList(): array
    {
        $converters = $this->converters;

        foreach ($converters as $converterId => $converterOptions) {
            $converters[$converterId]['id'] = $converterId;

            try {
                $converterInstance = ConverterFactory::makeConverter($converterId, $this->exampleImgFile, $this->exampleImgFileDesc, []);
                $converterInstance->checkOperationality();
                $converterInstance->doConvert();
                $converters[$converterId]['disabled'] = false;
            } catch (ConversionFailedException $conversionFailedException) {
                $converters[$converterId]['disabled'] = true;
            } catch (ConverterNotFoundException $converterNotFoundException) {
                $converters[$converterId]['disabled'] = true;
            }
        }

        return $converters;
    }

    public function getFirstAvailableConverter(): array
    {
        $list = $this->getConvertersList();

        foreach ($list as $converter) {
            if (!$converter['disabled']) {
                return $converter;
            }
        }

        return [];
    }
}
