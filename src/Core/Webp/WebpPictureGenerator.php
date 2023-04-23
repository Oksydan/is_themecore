<?php

namespace Oksydan\Module\IsThemeCore\Core\Webp;

class WebpPictureGenerator
{
    private $allowedExtensions = ['png', 'jpg', 'jpeg'];
    protected $content = '';
    private $doc;

    public function __construct($content)
    {
        $this->content = $content;
        $this->doc = new \DOMDocument();
    }

    public function loadContent()
    {
        $this->doc->loadHTML('<?xml encoding="utf-8" ?>' . $this->content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        return $this;
    }

    public function generatePictureTags(): void
    {
        $images = $this->doc->getElementsByTagName('img');

        if (0 === count($images)) {
            return;
        }

        foreach ($images as $image) {
            if ($image->hasAttribute('data-external-url')) {
                continue;
            }

            $this->generatePictureTagFromImg($image);
        }

        $this->content = $this->doc->saveHTML();
        $this->content = str_replace('<?xml encoding="utf-8" ?>', '', $this->content);
    }

    private function generatePictureTagFromImg($image)
    {
        $lazyLoad = !empty($params['lazyload']) ? $params['lazyload'] : (bool) preg_match('/' . implode('|', ['lazyload', 'swiper-lazy']) . '/i', $image->ownerDocument->saveHTML($image));
        $srcAttributePrefix = $lazyLoad ? 'data-' : '';
        $containSrcset = $image->hasAttribute($srcAttributePrefix . 'srcset');
        $srcAttribute = $srcAttributePrefix . ($containSrcset ? 'srcset' : 'src');

        $src = $image->getAttribute($srcAttribute);
        $rawSrcArray = explode(',', $src);
        $imageSrcArray = [];

        foreach ($rawSrcArray as $rawSrc) {
            $srcWithMediaArray = explode(' ', $rawSrc);

            $srcWithMediaArray = array_values(array_filter($srcWithMediaArray, function ($elem) {
                return !empty($elem);
            }));

            $imageSrcArray[] = [
                'file' => $srcWithMediaArray[0] ?? null,
                'media' => $srcWithMediaArray[1] ?? null,
                'ext' => isset($srcWithMediaArray[0]) ? pathinfo($srcWithMediaArray[0], PATHINFO_EXTENSION) : null,
            ];
        }

        $picture = $this->doc->createElement('picture');
        $pict_clone = $picture->cloneNode();
        $image->parentNode->replaceChild($pict_clone, $image);
        $pict_clone->appendChild($image);

        $source = $this->doc->createElement('source');
        $source->setAttribute('type', 'image/webp');
        $sourceWebp = '';

        $lastKey = array_key_last($imageSrcArray);

        foreach ($imageSrcArray as $key => $imageSrc) {
            $ext = explode('?', $imageSrc['ext']);
            $ext = $ext[0] ?? null;

            if (!in_array($ext, $this->allowedExtensions)) {
                continue;
            }

            $newWebpSrc = str_replace('.' . $imageSrc['ext'], '.webp', $imageSrc['file']);

            $sourceWebp .= $newWebpSrc . ($imageSrc['media'] ? ' ' . $imageSrc['media'] : '');

            if ($key != $lastKey) {
                $sourceWebp .= ', ';
            }
        }

        if ($sourceWebp) {
            $source->setAttribute($lazyLoad ? 'data-srcset' : 'srcset', $sourceWebp);
            $src_clone = $source->cloneNode();
            $image->parentNode->replaceChild($src_clone, $image);
            $src_clone->appendChild($image);
        }
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
