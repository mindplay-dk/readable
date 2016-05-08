mindplay/readable
=================

A few simple functions to format any kind of PHP value or type as human-readable.

This is not "better var_dump" - it won't color-code things or dump object state, there are
plenty of other libraries for that sort of thing.

Mainly this is intended to help you produce better error-messages:

```php
if (!is_int($value)) {
    throw new UnexpectedValueException("expected integer, got: " . readable::typeof($value));
} else if ($value > 100) {
    throw new RangeException("expected value up to 100, got: " . readable::value($value));
}
```

Presently, this library consists of these simple functions:

  * `readable::value($value)` formats any single PHP value as human-readable.
  * `readable::values($array)` formats an array of (mixed) values as human-readable.
  * `readable::typeof($value)` returns the type of value (or class name) for any given value.
  * `readable::callback($callable)` formats any `callable` as human-readable.

The latter function `callback()` will fall back to regular `value()` formatting if the given
value is not a callable - this function is preferable when a given value was expected to be
`callable`, e.g. recognizes function-names as strings and objects implementing `__invoke()`.

See the [test suite](test/test.php) for all formatting features.
