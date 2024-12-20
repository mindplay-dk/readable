mindplay/readable
=================

A few simple functions to format any kind of PHP value or type as human-readable.

[![PHP Version](https://img.shields.io/badge/php-8.0%2B-blue.svg)](https://packagist.org/packages/mindplay/readable)
[![Build Status](https://github.com/mindplay-dk/readable/actions/workflows/ci.yml/badge.svg)](https://github.com/mindplay-dk/readable/actions/workflows/ci.yml)
[![Code Coverage](https://scrutinizer-ci.com/g/mindplay-dk/readable/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mindplay-dk/readable/?branch=master)
[![License](https://img.shields.io/badge/license-MPL--2.0-green)](https://opensource.org/license/mpl-2-0)

Mainly, this is intended to help you produce better error-messages:

```php
if (!is_int($value)) {
    throw new UnexpectedValueException("expected integer, got: " . readable::typeof($value));
} else if ($value > 100) {
    throw new RangeException("expected value up to 100, got: " . readable::value($value));
}
```

Note that this library is not "better var_dump" - it won't color-code things or dump deep
object graphs. There are plenty of other libraries for that sort of thing.

Presently, this library consists of these simple functions:

  * `readable::value($value)` formats any single PHP value as human-readable.
  * `readable::values($array)` formats an array of (mixed) values as human-readable.
  * `readable::typeof($value)` returns the type of value (or class name) for any given value.
  * `readable::callback($callable)` formats any `callable` as human-readable.
  * `readable::severity($int)` returns for example `E_WARNING` as human-readable `"Warning"`.
  * `readable::error($exception)` returns a human-readable `Exception`/`Error` summary.
  * `readable::trace($trace)` formats a stack-trace with file-names, line-numbers, function-names and (optionally) arguments.
  * `readable::path($path)` removes the project root path from the start of a path.

The latter function `callback()` will fall back to regular `value()` formatting if the given
value is not a callable - this function is preferable when a given value was expected to be
`callable`, e.g. recognizes function-names as strings and objects implementing `__invoke()`.

See the [source code](src/readable.php) and [test suite](test/test.php) for all formatting features.
