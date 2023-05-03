<?php

namespace Oksydan\Module\IsThemeCore\Core\Partytown;

use Symfony\Component\Filesystem\Filesystem;

class FilesInstallation
{
    private \Is_themecore $module;

    public function __construct(
        \Is_themecore $module
    ) {
        $this->module = $module;
    }

    public function installFiles(): void
    {
        $this->installPartytown();
    }

    protected function getFileSystem(): Filesystem
    {
        return new Filesystem();
    }

    private function installPartytown(): void
    {
        $source = _PS_MODULE_DIR_ . $this->module->name . '/public/~partytown';
        $destination = _PS_ROOT_DIR_ . '/~partytown';
        $fileSystem = $this->getFileSystem();

        if (!file_exists($source)) {
            return;
        }

        if (file_exists($destination)) {
            $fileSystem->remove($destination);
        }

        $fileSystem->mkdir($destination);

        $directoryIterator = new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                $fileSystem->mkdir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                $fileSystem->copy($item, $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }
}
