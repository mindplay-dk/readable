<?php

class TestClass
{
    public function instanceMethod()
    {
    }

    public static function staticMethod()
    {
    }
}

class InvokableTestClass
{
    public function __invoke()
    {
    }
}

function empty_closure() {
    return function () {};
}

class TraceTest
{
    public function outer($arg)
    {
        $this->inner($arg);
    }

    protected function inner($arg)
    {
        $closure = function () use ($arg) {
            throw new Exception("got {$arg}");
        };

        $closure();
    }
}
