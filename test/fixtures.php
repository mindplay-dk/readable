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
