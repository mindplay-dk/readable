<?php

use mindplay\readable;
use function mindplay\testies\{ test, run, configure, eq, ok };

ini_set('zend.exception_ignore_args', '0'); // enable capture of exception arguments

require dirname(__DIR__) . '/vendor/autoload.php';

require __DIR__ . '/fixtures.php';

test(
    "can format values",
    function () {
        $unknown = fopen(__FILE__, "r");

        fclose($unknown); // closed file resources become "unknown types" in php

        $file = fopen(__FILE__, "r");

        eq(readable::value(array(1, 2, 3)), "[1, 2, 3]");
        eq(readable::value(array('foo' => 'bar', 'baz' => 'bat')), '["foo" => "bar", "baz" => "bat"]');
        eq(readable::value(true), "true");
        eq(readable::value(false), "false");
        eq(readable::value(null), "null");
        eq(readable::value(123), "123");
        eq(readable::value(0.42), "0.42");
        eq(readable::value(0.12345678), "~0.123457");
        eq(readable::value("hello"), '"hello"');
        eq(readable::value("hell\"o"), '"hell\"o"');
        eq(readable::value(new \stdClass()), '{object}');
        eq(readable::value(new TestClass()), '{TestClass}');
        eq(readable::value($file), '{stream}');
        eq(readable::value([new TestClass(), 'instanceMethod']), '{TestClass}->instanceMethod()');
        eq(readable::value(['TestClass', 'staticMethod']), 'TestClass::staticMethod()');
        eq(readable::value(empty_closure()), '{Closure in ' . __DIR__ . DIRECTORY_SEPARATOR . 'fixtures.php(22)}');
        eq(readable::value(new InvokableTestClass()), '{InvokableTestClass}');
        eq(readable::value($unknown), '{unknown type}');

        eq(readable::values(["foo", true, 1]), '"foo", true, 1');
        eq(readable::values(["hello" => "world"]), '"hello" => "world"');

        eq(readable::typeof(true), "bool");
        eq(readable::typeof(false), "bool");
        eq(readable::typeof(null), "null");
        eq(readable::typeof(null), "null");
        eq(readable::typeof(123), "int");
        eq(readable::typeof(1.23), "float");
        eq(readable::typeof("foo"), "string");
        eq(readable::typeof([]), "array");
        eq(readable::typeof(['TestClass', 'staticMethod']), "callable");
        eq(readable::typeof(new \stdClass), "object");
        eq(readable::typeof(new TestClass()), "TestClass");
        eq(readable::typeof($file), "resource");
        eq(readable::typeof($unknown), "unknown");

        eq(readable::callback(new InvokableTestClass()), '{InvokableTestClass}->__invoke()');
        eq(readable::callback([new TestClass(), 'instanceMethod']), '{TestClass}->instanceMethod()');
        eq(readable::callback(['TestClass', 'staticMethod']), 'TestClass::staticMethod()');
        eq(readable::callback(empty_closure()), '{Closure in ' . __DIR__ . DIRECTORY_SEPARATOR . 'fixtures.php(22)}');
        eq(readable::callback('is_array'), 'is_array()');

        fclose($file);

        readable::$max_string_length = 10;

        eq(readable::value('0123456789'), '"0123456789"');
        eq(readable::value('01234567890'), '"0123456789...[11]"');

        eq(readable::path(__FILE__), "test/test.php");
        eq(readable::path("/somewhere/else"), "/somewhere/else");

        foreach (readable::$severity_names as $value => $name) {
            eq(readable::severity($value), $name);
            eq(readable::error($value), $name);
        }

        eq(readable::severity(0), "{unknown error-code}");

        $test = new TraceTest();

        try {
            $test->outer("hello");
        } catch (Exception $e) {
            // caught
        }

        /**
         * @var Exception $e
         */

        ok(isset($e));
        ok($e instanceof Exception);

        $summary = readable::error($e);

        ok(
            preg_match('/^Exception with message: got hello in .*fixtures.php\(\d+\)$/', $summary) === 1,
            "exception summary has expected format",
            $summary
        );

        $trace = array_slice($e->getTrace(), 0, 3);

        $expected_trace = <<<TRACE
1. test/fixtures.php:38 TraceTest->{closure}()
2. test/fixtures.php:29 TraceTest->inner("hello")
3. test/test.php:83 TraceTest->outer("hello")
TRACE;

        eq(
            readable::trace($trace, true, true),
            $expected_trace,
            "stack-trace has expected format"
        );

        $long_trace = array_fill(0, 10, $trace[2]);

        $expected_long_trace = <<<TRACE
 1. test/test.php:83 TraceTest->outer()
 2. test/test.php:83 TraceTest->outer()
 3. test/test.php:83 TraceTest->outer()
 4. test/test.php:83 TraceTest->outer()
 5. test/test.php:83 TraceTest->outer()
 6. test/test.php:83 TraceTest->outer()
 7. test/test.php:83 TraceTest->outer()
 8. test/test.php:83 TraceTest->outer()
 9. test/test.php:83 TraceTest->outer()
10. test/test.php:83 TraceTest->outer()
TRACE;

        eq(
            readable::trace($long_trace, false, true),
            $expected_long_trace,
            "long stack-traces are corrently indented"
        );
    }
);

configure()->enableCodeCoverage(__DIR__ . '/build/clover.xml', dirname(__DIR__) . '/src');

exit(run());
