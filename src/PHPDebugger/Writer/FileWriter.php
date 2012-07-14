<?php

namespace PHPDebugger\Writer;

use PHPDebugger\Writer\WriterInterface;

class FileWriter implements WriterInterface
{
    private $runId;
    private $res;
    private $dir;
    private $buffer;
    private $bufferLength;
    private $maxBufferLength;

    public function __construct($dir, $maxBufferLength = 50)
    {
        if (!is_dir($dir)) {
            if (false === @mkdir($dir, 0777, true)) {
                throw new \InvalidArgumentException(sprintf('The directory "%s" does not exist, and could not be created.', $dir));
            }
        } else if (!is_writable($dir)) {
            throw new \InvalidArgumentException(sprintf('The directory "%s" is not writable.'));
        }

        if ($maxBufferLength <= 0) {
            throw new \InvalidArgumentException('$maxBufferLength must be greater than zero.');
        }

        $this->dir = $dir;
        $this->maxBufferLength = $maxBufferLength;
    }

    public function startRun($runId)
    {
        $this->runId = $runId;
        $this->res = fopen($this->dir.'/'.$runId, 'w');
        $this->buffer = array();
        $this->bufferLength = 0;
    }

    public function writeFunctionCall($functionName, array $args = array())
    {
        $this->buffer[] = $functionName.' '.microtime(true).' '.json_encode($this->formatter->makeSerializable($args));
        $this->bufferLength += 1;

        if ($this->bufferLength > $this->maxBufferLength) {
            $this->flushBuffer();
        }
    }

    public function writeMethodCall($className, $method, array $args = array())
    {
        $this->buffer[] = $className.'::'.$method.' '.microtime(true).' '.json_encode($this->formatter->makeSerializable($args));
        $this->bufferLength += 1;

        if ($this->bufferLength > $this->maxBufferLength) {
            $this->flushBuffer();
        }
    }

    public function stopRun($runId)
    {
        $this->flushBuffer();
        fclose($this->res);
    }

    public function __destruct()
    {
        $this->stopRun($this->runId);
    }

    private function flushBuffer()
    {
        if ( ! $this->buffer) {
            return;
        }

        fputs($this->res, implode("\n", $this->buffer)."\n");
        $this->buffer = array();
        $this->bufferLength = 0;
    }
}