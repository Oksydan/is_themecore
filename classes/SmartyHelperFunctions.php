<?php

class SmartyHelperFunctions {

    public static function generateImagesSources($params) {
      $image = $params['image'];
      $size = $params['size'];
      $lazyLoad = isset($params['lazyload']) ? $params['lazyload'] : true;
      $attributes = [];
      $highDpiImagesEnabled = (bool) Configuration::get('PS_HIGHT_DPI');

      $srcAttributePrefix = $lazyLoad ? 'data-' : '';

      $img = $image['bySize'][$size]['url'];

      if ($highDpiImagesEnabled) {
        $size2x = $size . '2x';
        $img2x = str_replace($size, $size2x, $img);
        $attributeName = $srcAttributePrefix . 'srcset';
        $attributes[$attributeName] = "$img, $img2x 2x";
      } else {
        $attributeName = $srcAttributePrefix . 'src';
        $attributes[$attributeName] = $img;
      }

      if ($lazyLoad) {
        $width = $image['bySize'][$size]['width'];
        $height = $image['bySize'][$size]['height'];
        $placeholderSrc = self::generateImageSvgPlaceholder(['width' => $width, 'height' => $height]);

        $attributes['src'] = $placeholderSrc;
      }

      $attributesToPrint = [];

      foreach ($attributes as $attr => $value) {
        $attributesToPrint[] = $attr . '="' . $value . '"';
      }

      return implode($attributesToPrint, PHP_EOL);
    }

    public static function generateImageSvgPlaceholder($params) {
      $width = $params['width'];
      $height = $params['height'];

      return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='$width' height='$height' viewBox='0 0 1 1'%3E%3C/svg%3E";
    }
}
