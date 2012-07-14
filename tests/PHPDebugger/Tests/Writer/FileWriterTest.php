<?php

namespace PHPDebugger\Tests\Writer;

use Symfony\Component\Filesystem\Filesystem;
use PHPDebugger\Writer\FileWriter;

class FileWriterTest extends \PHPUnit_Framework_TestCase
{
    private $dir;
    private $fs;
    private $writer;

    public function testWriteFiles()
    {
        $this->assertFileNotExists($this->dir.'/test');
        $this->writer->startRun('test');
        $this->assertFileExists($this->dir.'/test');

        $this->assertEquals('', file_get_contents($this->dir.'/test'));
        $this->writer->writeFunctionCall('foo');
        $this->writer->writeFunctionCall('bar');
        $this->assertEquals('', file_get_contents($this->dir.'/test'));
        $this->writer->writeFunctionCall('baz');
        $this->assertEquals('', file_get_contents($this->dir.'/test'));
    }

    protected function setUp()
    {
        $this->dir = sys_get_temp_dir().'/php-debugger-fw-test';

        $this->fs = new Filesystem();
        $this->fs->remove($this->dir);

        $this->writer = new FileWriter($this->dir, 3);
    }

    protected function tearDown()
    {
        $this->fs->remove($this->dir);
    }
}