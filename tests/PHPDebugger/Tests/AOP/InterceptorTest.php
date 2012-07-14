<?php

namespace PHPDebugger\Tests\AOP;

use PHPDebugger\AOP\Interceptor;

class InterceptorTest extends \PHPUnit_Framework_TestCase
{
    private $interceptor;

    protected function setUp()
    {
        if ( ! extension_loaded('AOP')) {
            $this->markTestSkipped('The AOP extension is not loaded.');
        }

        $this->interceptor = new Interceptor();
    }

    public function testAddFunctionCallback()
    {
        $calls = array();
        $this->interceptor->addBeforeFunctionCallback(function($functionName, $args) use (&$calls) {
            $calls[] = array($functionName, $args);
        });

        get_class($this);
        get_class($this->interceptor);

        $this->assertCount(2, $calls);
        $this->assertEquals('get_class', $calls[0][0]);
        $this->assertSame(array($this), $calls[0][1]);
        $this->assertEquals('get_class', $calls[1][0]);
        $this->assertSame(array($this->interceptor), $calls[1][1]);
    }

    /**
     * @group foo
     */
    public function testAddMethodCallback()
    {
        $calls = array();
        $this->interceptor->addBeforeMethodCallback(function($className, $methodName, $args) use (&$calls) {
            $calls[] = array($className, $methodName, $args);
        });

        $foo = new FooClass();
        $bar = new BarClass();

        $foo->foo(1, 2, 3);
        $bar->fooBar(array('foo'));

        $this->assertCount(3, $calls);

        $this->assertEquals('PHPDebugger\Tests\AOP\FooClass', $calls[0][0]);
        $this->assertEquals('foo', $calls[0][1]);
        $this->assertSame(array(1, 2, 3), $calls[0][2]);

        $this->assertEquals('PHPDebugger\Tests\AOP\BarClass', $calls[1][0]);
        $this->assertEquals('fooBar', $calls[1][1]);
        $this->assertSame(array(array('foo')), $calls[1][2]);

        $this->assertEquals('PHPDebugger\Tests\AOP\FooClass', $calls[2][0]);
        $this->assertEquals('bar', $calls[2][1]);
        $this->assertSame(array(), $calls[2][2]);
    }
}

class FooClass {
    public function foo() { }
    protected function bar() { }
}

class BarClass extends FooClass {
    public function fooBar() {
        $this->bar();
    }
}