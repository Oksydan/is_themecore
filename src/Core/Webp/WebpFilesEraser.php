<?php

namespace Oksydan\Module\IsThemeCore\Core\Webp;

use Symfony\Component\Finder\Finder;

class WebpFilesEraser
{
    private $query = '';
    private $finder;
    private $files;
    private $excludeList = ['node_modules', 'vendor', 'app', 'var', 'classes', 'controllers', 'download'];
    private $filesCount = 0;

    public function __construct()
    {
        $this->finder = new Finder();
    }

    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function setExcludeList(array $excludeList)
    {
        $this->excludeList = $excludeList;

        return $this;
    }

    public function getExcludeList()
    {
        return $this->excludeList;
    }

    private function setFilesCount()
    {
        $this->filesCount = iterator_count($this->files);

        return $this;
    }

    public function getFilesCount()
    {
        return $this->filesCount;
    }

    private function findFiles()
    {
        $this->files = $this->finder
            ->files()
            ->ignoreUnreadableDirs()
            ->in($this->query)
            ->exclude($this->excludeList)
            ->name('*.webp');
    }

    public function eraseFiles()
    {
        $this->findFiles();
        $this->setFilesCount();

        foreach ($this->files as $file) {
            try {
                unlink($file->getPathname());
            } catch (\Throwable $error) {
                throw $error;
            }
        }
    }
}
