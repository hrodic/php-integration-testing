<?php declare(strict_types=1);

namespace IntegrationTesting\Driver;

use ArrayIterator;
use FilesystemIterator;
use Iterator;

class FileSystem
{
    /**
     * @param string $path
     * @param string $extension
     * @return Iterator
     * @throws TestingException
     */
    public static function getFileListIteratorFromPathByExtension(string $path, string $extension): Iterator
    {
        $realPath = realpath($path);
        if (!$realPath) {
            throw new TestingException("The path [$path] is not valid");
        }
        $iterator = new FilesystemIterator($realPath);
        $fileList = new ArrayIterator();
        foreach ($iterator as $path => $fileInfo) {
            if ($fileInfo->isFile() && $fileInfo->isReadable() && $fileInfo->getExtension() === $extension) {
                $fileList->append($path);
            }
        }
        return $fileList;
    }

    /**
     * @param Iterator $iterator
     * @param callable $callback
     */
    public static function runCallbackOnEachFileIteratorContents(Iterator $iterator, callable $callback): void
    {
        foreach ($iterator as $filePath) {
            $callback(file_get_contents($filePath));
        }
    }
}
