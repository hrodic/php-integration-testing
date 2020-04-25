<?php declare(strict_types=1);

namespace IntegrationTesting\Driver;

use ArrayIterator;
use FilesystemIterator;
use IntegrationTesting\Exception\TestingException;
use Iterator;

/**
 * Class FileSystem
 * @package IntegrationTesting\Driver
 * @internal NOT FOR PUBLIC USE
 */
class FileSystem
{
    /**
     * @param string $path
     * @return string
     * @throws TestingException
     */
    private function getRealPath(string $path): string
    {
        $realPath = realpath($path);
        if (!$realPath) {
            throw new TestingException("The path [$path] is not valid");
        }
        return $realPath;
    }

    public function getFileContents(string $path): string
    {
        $path = $this->getRealPath($path);
        if (!is_readable($path)) {
            throw new TestingException("Filepath [$path] is not readable");
        }
        return file_get_contents($path);
    }

    /**
     * @param string $path
     * @param string $extension
     * @return Iterator
     * @throws TestingException
     */
    public function getFileListIteratorFromPathByExtension(string $path, string $extension): Iterator
    {
        $iterator = new FilesystemIterator($this->getRealPath($path));
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
     * @throws TestingException
     */
    public function runCallbackOnEachFileIteratorContents(Iterator $iterator, callable $callback): void
    {
        foreach ($iterator as $filePath) {
            $callback($this->getFileContents($filePath));
        }
    }
}
