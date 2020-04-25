<?php declare(strict_types=1);

namespace IntegrationTesting\Driver;

use IntegrationTesting\Exception\TestingException;
use PHPUnit\Framework\TestCase;

/**
 * Class FileSystemTest
 * @package IntegrationTesting\Driver
 * @covers \IntegrationTesting\Driver\FileSystem
 */
class FileSystemTest extends TestCase
{
    /**
     * @var FileSystem
     */
    private $sut;
    private $tmpDir;

    public function setUp(): void
    {
        $this->sut = new FileSystem();
        $this->tmpDir = 'build/tmp';
        mkdir($this->tmpDir, 0777, true);
    }

    public function tearDown(): void
    {
        rmdir($this->tmpDir);
    }

    public function testGetFileContentsOk(): void
    {
        $filePath = $this->tmpDir . DIRECTORY_SEPARATOR . uniqid() . '.test';
        file_put_contents($filePath, 'contents');
        $content = $this->sut->getFileContents($filePath);
        unlink($filePath);
        $this->assertSame('contents', $content);
    }

    public function testGetFileContentsException(): void
    {
        $this->expectException(TestingException::class);
        $this->sut->getFileContents('not-valid');
    }

    public function testGetFileListIteratorFromPathByExtension(): void
    {
        $filePath = $this->tmpDir . DIRECTORY_SEPARATOR . uniqid() . '.test';
        file_put_contents($filePath, '');
        $iterator = $this->sut->getFileListIteratorFromPathByExtension($this->tmpDir, 'test');
        unlink($filePath);
        $this->assertSame(1, count($iterator));
    }

    public function testRunCallbackOnEachFileIteratorContents(): void
    {
        $filePath = $this->tmpDir . DIRECTORY_SEPARATOR . uniqid() . '.test';
        file_put_contents($filePath, 'test');
        $receivedContents = null;
        $callable = function ($contents) use (&$receivedContents) {
            $receivedContents = $contents;
        };
        $iterator = new \ArrayIterator([$filePath]);
        $this->sut->runCallbackOnEachFileIteratorContents($iterator, $callable);
        unlink($filePath);
        $this->assertSame('test', $receivedContents);
    }
}
