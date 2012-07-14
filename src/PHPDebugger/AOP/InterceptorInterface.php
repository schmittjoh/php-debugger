<?php

namespace PHPDebugger\AOP;

use PHPDebugger\Logger\LoggerInterface;

interface InterceptorInterface
{
    function addBeforeMethodCallback($callback);
    function addBeforeFunctionCallback($callback);
}