<?php

declare(strict_types=1);

namespace Core\Config\Generator\Util;

final class DirectoryManager
{
    public function clearDirectory(string $dir): void
    {
        $this->deleteFolderContents($dir);
    }

    private function deleteFolderContents(string $folder): void
    {
        if (!is_dir($folder)) {
            return;
        }

        $files = array_diff(scandir($folder), ['.', '..']);
        foreach ($files as $file) {
            $filePath = $folder . DIRECTORY_SEPARATOR . $file;
            if (is_dir($filePath)) {
                $this->deleteFolderContents($filePath);
                rmdir($filePath);
            } else {
                unlink($filePath);
            }
        }
    }
}
