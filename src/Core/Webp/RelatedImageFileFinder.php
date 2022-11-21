<?php

namespace Oksydan\Module\IsThemeCore\Core\Webp;

class RelatedImageFileFinder
{
    protected $allowedImagesExtensions = ['jpg', 'png', 'jpeg'];

    public function setAllowedImagesExtensions($allowedImagesExtensions)
    {
        $this->allowedImagesExtensions = $allowedImagesExtensions;

        return $this;
    }

    public function getAllowedImagesExtensions()
    {
        return $this->allowedImagesExtensions;
    }

    public function findFile($relatedFile)
    {
        $fileData = pathinfo($relatedFile);
        $possibleFiles = [];

        $extensions = $this->getAllowedImagesExtensions();

        foreach ($extensions as $ext) {
            $possibleFiles[] = $fileData['dirname'] . '/' . $fileData['filename'] . '.' . $ext;
        }

        foreach ($possibleFiles as $file) {
            if (file_exists($file)) {
                return $file;
            }
        }
    }
}
