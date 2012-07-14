<?php

namespace PHPDebugger\Writer;

interface WriterInterface
{
    function startRun($runId);
    function writeMethodCall($className, $method, array $args = array());
    function writeFunctionCall($functionName, array $args = array());
    function stopRun($runId);
}