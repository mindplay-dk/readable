<?php

namespace mindplay;

use Closure;
use ReflectionFunction;

/**
 * Pseudo-namespace for functions that generate human-readable string representations
 * of most types of PHP values.
 */
abstract class readable
{
    /**
     * @var int strings longer than this number of characters will be truncated when formatting string-values
     */
    public static $max_string_length = 120;

    /**
     * @param mixed $value any type of PHP value
     *
     * @return string readable representation of the given value
     */
    public static function value($value)
    {
        $type = is_array($value) && is_callable($value)
            ? "callable"
            : strtolower(gettype($value));

        switch ($type) {
            case "boolean":
                return $value ? "true" : "false";

            case "integer":
                return number_format($value, 0, "", "");

            case "double": // (for historical reasons "double" is returned in case of a float, and not simply "float")
                $formatted = sprintf("%.6g", $value);

                return $value == $formatted
                    ? "{$formatted}"
                    : "~{$formatted}";

            case "string":
                $string = strlen($value) > self::$max_string_length
                    ? substr($value, 0, self::$max_string_length) . "...[" . strlen($value) . "]"
                    : $value;

                return '"' . addslashes($string) . '"';

            case "array":
                return "[" . self::values($value) . "]";

            case "object":
                if ($value instanceof Closure) {
                    $reflection = new ReflectionFunction($value);

                    return "{Closure in " . $reflection->getFileName() . "({$reflection->getStartLine()})}";
                }
                
                return "{" . ($value instanceof \stdClass ? "object" : get_class($value)) . "}";

            case "resource":
                return "{" . get_resource_type($value) . "}";

            case "callable":
                return is_object($value[0])
                    ? '{' . get_class($value[0]) . "}->{$value[1]}()"
                    : "{$value[0]}::{$value[1]}()";
            
            case "null":
                return "null";
        }

        return "{{$type}}"; // "unknown type" and possibly unsupported (future) types
    }

    /**
     * @param array $array array containing any type of PHP values
     *                     
     * @return string comma-separated human-readable representation of the given values
     */
    public static function values(array $array)
    {
        $formatted = array_map(['mindplay\\readable', 'value'], $array);

        if (array_keys($array) !== range(0, count($array) - 1)) {
            foreach ($formatted as $name => $value) {
                $formatted[$name] = self::value($name) . " => {$value}";
            }
        }

        return implode(", ", $formatted);
    }

    /**
     * @param mixed $value any type of PHP value
     * 
     * @return string human-readable type-name
     */
    public static function typeof($value)
    {
        $type = !is_string($value) && !is_object($value) && is_callable($value)
            ? "callable"
            : strtolower(gettype($value));

        switch ($type) {
            case "boolean":
                return "bool";
            case "integer":
                return "int";
            case "double":
                return "float";
            case "object":
                return $value instanceof \stdClass ? "object" : get_class($value);
            case "string":
            case "array":
            case "resource":
            case "callable":
            case "null":
                return $type;
        }
        
        return "unknown";
    }

    /**
     * @param mixed $callable
     * 
     * @return string human-readable description of callback
     */
    public static function callback($callable)
    {
        if (is_string($callable) && is_callable($callable)) {
            return "{$callable}()";
        } elseif (is_object($callable) && method_exists($callable, "__invoke")) {
            return $callable instanceof Closure
                ? self::value($callable)
                : "{" . get_class($callable) . "}->__invoke()";
        }
        
        return self::value($callable);
    }
}
