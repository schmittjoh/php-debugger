<?php

namespace PHPDebugger\AOP;

/**
 * Default Interceptor implementation which uses the AOP extension.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class Interceptor implements InterceptorInterface
{
    public function __construct()
    {
        if ( ! extension_loaded('AOP')) {
            throw new \LogicException('The AOP extension must be enabled. Please see https://github.com/AOP-PHP/AOP');
        }
    }

    public function addBeforeFunctionCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('$callback must be a valid callable.');
        }

        aop_add_before('**()', function(\AopTriggeredJoinPoint $joinPoint) use ($callback) {
            $callback($joinPoint->getTriggeringFunctionName(), $joinPoint->getArguments());
        });
    }

    public function addBeforeMethodCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('$callback must be a valid callable.');
        }

        aop_add_before('*->*()', function(\AopTriggeredJoinPoint $joinPoint) use ($callback) {
            $callback($joinPoint->getTriggeringClassName(), $joinPoint->getTriggeringMethodName(), $joinPoint->getArguments());
        });
    }
}