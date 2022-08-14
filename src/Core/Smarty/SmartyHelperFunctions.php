<?php

namespace Oksydan\Module\IsThemeCore\Core\Smarty;

class SmartyHelperFunctions {

    public static function generateImagesSources($params) {
      $image = $params['image'];
      $size = $params['size'];
      $lazyLoad = isset($params['lazyload']) ? $params['lazyload'] : true;
      $attributes = [];
      $highDpiImagesEnabled = (bool) \Configuration::get('PS_HIGHT_DPI');

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

    public static function appendParamToUrl($params) {
      list(
        'url' => $url,
        'key' => $key,
        'value' => $value
      ) = $params;

      $query = parse_url($url, PHP_URL_QUERY);

      if ($query) {
        parse_str($query, $queryParams);
        $queryParams[$key] = $value;
        $url = str_replace("?$query", '?' . http_build_query($queryParams), $url);
      } else {
        $url .= '?' . urlencode($key) . '=' . urlencode($value);
      }

      return $url;
    }

    public static function imagesBlock($params, $content, $smarty)
    {
      $webpEnabled = !empty($params['webpEnabled']) ? $params['webpEnabled'] : false;

      if ($webpEnabled && !empty($content)) {
        $doc = new \DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $images = $doc->getElementsByTagName('img');

        if (0 === count($images)) {
          return $content;
        }

        foreach ($images as $image) {
          $lazyLoad = !empty($params['lazyload']) ? $params['lazyload'] : (bool) preg_match('/' . implode('|', ['lazyload', 'swiper-lazy']) . '/i', $image->ownerDocument->saveHTML($image));
          $srcAttributePrefix = $lazyLoad ? 'data-' : '';
          $containSrcset =  $image->hasAttribute($srcAttributePrefix . 'srcset');
          $srcAttribute = $srcAttributePrefix . ($containSrcset ? 'srcset' : 'src');

          $src = $image->getAttribute($srcAttribute);
          $rawSrcArray = explode(',', $src);
          $imageSrcArray = [];

          foreach($rawSrcArray as $rawSrc) {
            $srcWithMediaArray = explode(' ', $rawSrc);

            $srcWithMediaArray = array_values(array_filter($srcWithMediaArray, function($elem) {
              return !empty($elem);
            }));

            $imageSrcArray[] = [
              'file' => isset($srcWithMediaArray[0]) ? $srcWithMediaArray[0] : null,
              'media' => isset($srcWithMediaArray[1]) ? $srcWithMediaArray[1] : null,
              'ext' => isset($srcWithMediaArray[0]) ? pathinfo($srcWithMediaArray[0], PATHINFO_EXTENSION) : null,
            ];
          }

          $picture = $doc->createElement('picture');
          $pict_clone = $picture->cloneNode();
          $image->parentNode->replaceChild($pict_clone, $image);
          $pict_clone->appendChild($image);

          $source = $doc->createElement('source');
          $source->setAttribute('type', 'image/webp');
          $sourceWebp = '';

          $lastKey = array_key_last($imageSrcArray);

          foreach($imageSrcArray as $key => $imageSrc)  {
            $newWebpSrc = str_replace('.' . $imageSrc['ext'], '.webp', $imageSrc['file']);

            $sourceWebp.= $newWebpSrc . ($imageSrc['media'] ? ' ' . $imageSrc['media'] : '');

            if ($key != $lastKey) {
              $sourceWebp.= ', ';
            }
          }

          $source->setAttribute(($lazyLoad ? 'data-srcset' : 'srcset'), $sourceWebp);
          $src_clone = $source->cloneNode();
          $image->parentNode->replaceChild($src_clone, $image);
          $src_clone->appendChild($image);
        }

        $content = $doc->saveHTML();
        $content = str_replace('<?xml encoding="utf-8" ?>', '', $content);

        return $content;
      }
    }
}
