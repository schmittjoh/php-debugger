<?php

namespace PHPDebugger;

use PHPDebugger\Writer\FileWriter;

use PHPDebugger\Writer\WriterInterface;

use PHPDebugger\AOP\InterceptorInterface;
use PHPDebugger\AOP\Interceptor;

/**
 * Debugger for PHP.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class PHPDebugger
{
    private $runId;
    private $callbacksAdded = false;
    private $interceptor;
    private $writer;

    public static function createDefault($directory)
    {
        return new self(new Interceptor(), new FileWriter($directory));
    }

    public function __construct(InterceptorInterface $interceptor, WriterInterface $writer)
    {
        $this->interceptor = $interceptor;
        $this->writer = $writer;
    }

    public function isEnabled()
    {
        return null !== $this->runId;
    }

    public function enable()
    {
        if (null !== $this->runId) {
            return;
        }
        $this->runId = uniqid(mt_rand(), true);
        $this->writer->startRun($this->runId);

        // The default implementation of the InterceptorInterface does not
        // provide an API for removing added advices. Therefore, we need
        // to cope with this here.
        if ($this->callbacksAdded) {
            return;
        }
        $this->callbacksAdded = true;

        $enabled = &$this->enabled;
        $writer = $this->writer;
        $this->interceptor->addBeforeMethodCallback(
            function($className, $methodName, array $arguments = array()) use (&$enabled, $writer) {
                if ( ! $enabled) {
                    return;
                }

                $writer->writeClassCall($className, $methodName, $arguments);
        });
        $this->interceptor->addBeforeFunctionCallback(
            function($functionName, array $arguments = array()) use (&$enabled, $writer) {
                if ( ! $enabled) {
                    return;
                }

                $writer->writeFunctionCall($functionName, $arguments);
        });
    }

    public function getRunId()
    {
        return $this->runId;
    }

    public function disable()
    {
        $runId = $this->runId;
        $this->writer->stopRun($runId);
        $this->runId = null;

        return $runId;
    }
}