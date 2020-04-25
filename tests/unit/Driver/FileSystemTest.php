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

    public function setUp(): void
    {
        $this->sut = new FileSystem();
    }

    public function testGetFileContentsOk(): void
    {
        $file = tmpfile();
        $path = stream_get_meta_data($file)['uri'];
        file_put_contents($path, 'contents');
        $content = $this->sut->getFileContents($path);
        fclose($file);
        $this->assertSame('contents', $content);
    }

    public function testGetFileContentsException(): void
    {
        $this->expectException(TestingException::class);
        $this->sut->getFileContents('not-valid');
    }

    public function testGetFileListIteratorFromPathByExtension(): void
    {
        $tmpDir = sys_get_temp_dir();
        $filePath = $tmpDir . DIRECTORY_SEPARATOR . uniqid() . '.test';
        file_put_contents($filePath, '');
        unlink($filePath);
        $iterator = $this->sut->getFileListIteratorFromPathByExtension($tmpDir, 'test');
        $this->assertSame(1, count($iterator));
    }

    public function testRunCallbackOnEachFileIteratorContents(): void
    {
        $this->markTestIncomplete();
    }
}
