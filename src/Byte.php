<?php

namespace Bermuda\Stdlib;

/**
 * Class Byte
 *
 * Represents a quantity of bytes and provides methods for converting between different units
 * (e.g., B, kB, MB, GB, TB, etc.). It offers utility functions for arithmetic operations, comparisons,
 * and human-readable formatting. The class implements the {@see \Stringable} interface, so it can be directly
 * cast to a string using a humanized format.
 */
final class Byte implements \Stringable
{
    public const int COMPARE_LT = -1;
    public const int COMPARE_EQ = 0;
    public const int COMPARE_GT = 1;

    // Define comparison modes
    public const string MODE_ALL = 'all';
    public const string MODE_ANY = 'any';

    /**
     * The value in bytes, stored as an integer or float.
     *
     * @var int|float
     */
    public readonly int|float $value;

    /**
     * Mapping of units to their corresponding exponential factors.
     * For example, 'GB' corresponds to 3 (i.e. 1024^3 bytes).
     *
     * @var array<string, int>
     */
    private const units = [
        'YB' => 8, 'ZB' => 7, 'EB' => 6,
        'PB' => 5, 'TB' => 4, 'GB' => 3,
        'MB' => 2, 'kB' => 1, 'B'  => 0
    ];

    /**
     * The amount to use as a multiplier between units (typically 1024).
     *
     * @var int
     */
    private const amount = 1024;

    /**
     * Byte constructor.
     *
     * Accepts an int, float, or string representing a byte quantity and converts it to a numeric value in bytes.
     *
     * @param int|float|string $value The byte value to parse and store.
     */
    public function __construct(int|float|string $value)
    {
        $this->value = self::parse($value);
    }

    /**
     * Creates a new Byte instance.
     *
     * This static factory method accepts an integer or a string representation of a byte value.
     *
     * @param int|string $value The value to convert.
     * @return static
     */
    public static function new(int|string $value): self
    {
        return new self(is_int($value) ? $value : self::parse($value));
    }

    /**
     * Creates a new Byte instance from a byte (B) value.
     *
     * @param int $value The number of bytes.
     * @return static A new Byte instance.
     */
    public static function b(int $value): self
    {
        return new self($value);
    }

    /**
     * Creates a new Byte instance from a kilobyte (kB) value.
     *
     * @param int $value The number of kilobytes.
     * @return static A new Byte instance with the byte equivalent.
     */
    public static function kb(int $value): self
    {
        return new self($value*pow(self::amount, self::units['kB']));
    }

    /**
     * Creates a new Byte instance from a megabyte (MB) value.
     *
     * @param int $value The number of megabytes.
     * @return static A new Byte instance with the byte equivalent.
     */
    public static function mb(int $value): self
    {
        return new self($value*pow(self::amount, self::units['MB']));
    }

    /**
     * Creates a new Byte instance from a gigabyte (GB) value.
     *
     * @param int $value The number of gigabytes.
     * @return static A new Byte instance with the byte equivalent.
     */
    public static function gb(int $value): self
    {
        return new self($value*pow(self::amount, self::units['GB']));
    }

    /**
     * Creates a new Byte instance from a terabyte (TB) value.
     *
     * @param int $value The number of terabytes.
     * @return static A new Byte instance with the byte equivalent.
     */
    public static function tb(int $value): self
    {
        return new self($value*pow(self::amount, self::units['TB']));
    }

    /**
     * Creates a new Byte instance from a petabyte (PB) value.
     *
     * @param int $value The number of petabytes.
     * @return static A new Byte instance with the byte equivalent.
     */
    public static function pb(int $value): self
    {
        return new self($value*pow(self::amount, self::units['PB']));
    }

    /**
     * Creates a new Byte instance from an exabyte (EB) value.
     *
     * @param int $value The number of exabytes.
     * @return static A new Byte instance with the byte equivalent.
     */
    public static function eb(int $value): self
    {
        return new self($value*pow(self::amount, self::units['EB']));
    }

    /**
     * Creates a new Byte instance from a zettabyte (ZB) value.
     *
     * @param int $value The number of zettabytes.
     * @return static A new Byte instance with the byte equivalent.
     */
    public static function zb(int $value): self
    {
        return new self($value*pow(self::amount, self::units['ZB']));
    }

    /**
     * Creates a new Byte instance from a yottabyte (YB) value.
     *
     * @param int $value The number of yottabytes.
     * @return static A new Byte instance with the byte equivalent.
     */
    public static function yb(int $value): self
    {
        return new self($value*pow(self::amount, self::units['YB']));
    }

    /**
     * Creates a Byte instance from a value in any supported unit
     *
     * @param int|float $value The numeric value
     * @param string $unit The unit (B, kB, MB, etc.)
     * @return static A new Byte instance
     * @throws \InvalidArgumentException If the unit is not supported
     */
    public static function from(int|float $value, string $unit): self
    {
        $unit = strtoupper($unit);

        foreach (self::units as $supportedUnit => $exponent) {
            if (strcasecmp($unit, $supportedUnit) === 0) {
                return new self($value * pow(self::amount, $exponent));
            }
        }

        throw new \InvalidArgumentException("Unsupported unit: $unit");
    }

    /**
     * Converts the Byte instance to a human-readable string.
     *
     * This method calls the humanize() method to format the byte value using the most appropriate unit.
     *
     * @return string The human-readable form of the byte value.
     */
    public function __toString(): string
    {
        return self::humanize($this->value);
    }

    /**
     * Returns a humanized string representation of the byte value.
     *
     * Alias of __toString().
     *
     * @return string A human-readable string focusing on the appropriate unit.
     */
    public function toString(): string
    {
        return self::humanize($this->value);
    }

    /**
     * Converts the byte value to a specified unit and returns a formatted string.
     *
     * Supported units are defined in the class constant units.
     *
     * @param string $units   The unit to convert to (default is 'b'). Case-insensitive.
     * @param int|null $precision  The number of decimals to round to. If null, no rounding is applied.
     * @param string $delim   A delimiter to insert between the numeric value and the unit.
     * @return string The converted value formatted as a string.
     */
    public function to(string $units = 'b', ?int $precision = null, string $delim = ' '): string
    {
        foreach (self::units as $unit => $exponent) {
            if (strcasecmp($units, $unit) === 0) {
                if ($precision !== null) {
                    return round($this->value / pow(self::amount, $exponent), $precision)
                        . "$delim$unit";
                }

                return $this->value / pow(self::amount, $exponent)
                    . "$delim$unit";
            }
        }

        return $this->value / self::amount . "{$delim}B";
    }

    /**
     * Converts the byte value to a kilobyte (kB) formatted string.
     *
     * This is a convenience method that calls the to() method with the "kb" unit.
     *
     * @param int|null $precision Optional precision for rounding the result; if null, no rounding is applied.
     * @param string $delim The delimiter between the numeric value and the unit (default is a space).
     * @return string The formatted string representing the value in kilobytes.
     */
    public function toKb(?int $precision = null, string $delim = ' '): string
    {
        return $this->to('kb', $precision, $delim);
    }

    /**
     * Converts the byte value to a megabyte (MB) formatted string.
     *
     * This convenience method calls the to() method with the "mb" unit.
     *
     * @param int|null $precision Optional precision for rounding.
     * @param string $delim The delimiter between the value and the unit.
     * @return string The value formatted in megabytes.
     */
    public function toMb(?int $precision = null, string $delim = ' '): string
    {
        return $this->to('mb', $precision, $delim);
    }

    /**
     * Converts the byte value to a gigabyte (GB) formatted string.
     *
     * This method calls to() using the "gb" unit to produce a human-readable output.
     *
     * @param int|null $precision Optional number of decimals.
     * @param string $delim The delimiter to separate the number and the unit.
     * @return string The formatted string in gigabytes.
     */
    public function toGb(?int $precision = null, string $delim = ' '): string
    {
        return $this->to('gb', $precision, $delim);
    }

    /**
     * Converts the byte value to a terabyte (TB) formatted string.
     *
     * This convenience method uses the to() method with the "tb" unit.
     *
     * @param int|null $precision Optional precision (decimal places) for rounding.
     * @param string $delim The delimiter between the number and the unit.
     * @return string The formatted string representing the value in terabytes.
     */
    public function toTb(?int $precision = null, string $delim = ' '): string
    {
        return $this->to('tb', $precision, $delim);
    }

    /**
     * Converts the byte value to a petabyte (PB) formatted string.
     *
     * Calls the to() method with the "pb" unit.
     *
     * @param int|null $precision Optional precision for the numeric value.
     * @param string $delim The delimiter separating the number and unit.
     * @return string The value formatted in petabytes.
     */
    public function toPb(?int $precision = null, string $delim = ' '): string
    {
        return $this->to('pb', $precision, $delim);
    }

    /**
     * Converts the byte value to an exabyte (EB) formatted string.
     *
     * This method is a convenience wrapper over to() with the "eb" unit.
     *
     * @param int|null $precision The precision for the result.
     * @param string $delim The delimiter between the numeric value and the unit.
     * @return string A string representing the value in exabytes.
     */
    public function toEb(?int $precision = null, string $delim = ' '): string
    {
        return $this->to('eb', $precision, $delim);
    }

    /**
     * Converts the byte value to a zettabyte (ZB) formatted string.
     *
     * Uses the to() method with the "zb" unit to transform the numeric value.
     *
     * @param int|null $precision Optional decimal precision.
     * @param string $delim A delimiter string between the number and unit.
     * @return string The formatted string in zettabytes.
     */
    public function toZb(?int $precision = null, string $delim = ' '): string
    {
        return $this->to('zb', $precision, $delim);
    }

    /**
     * Converts the byte value to a yottabyte (YB) formatted string.
     *
     * This convenience method returns a human-readable string using the "yb" unit by calling to().
     *
     * @param int|null $precision Optional precision for rounding.
     * @param string $delim The delimiter to insert between the numeric value and the unit.
     * @return string The value formatted in yottabytes.
     */
    public function toYb(?int $precision = null, string $delim = ' '): string
    {
        return $this->to('yb', $precision, $delim);
    }

    /**
     * Returns the raw numeric value in a specific unit without formatting.
     *
     * @param string $unit The target unit (e.g., 'kb', 'mb', 'gb')
     * @param int|null $precision Optional precision for rounding
     * @return float|int The numeric value in the specified unit
     */
    public function getValue(string $unit, ?int $precision = null): float|int
    {
        foreach (self::units as $unitKey => $exponent) {
            if (strcasecmp($unit, $unitKey) === 0) {
                $value = $this->value / pow(self::amount, $exponent);
                return $precision !== null ? round($value, $precision) : $value;
            }
        }

        throw new \InvalidArgumentException("Unsupported unit: $unit");
    }

    /**
     * Compares this Byte instance with another value or array of values.
     *
     * When comparing with a single value:
     * - Returns self::COMPARE_LT if this instance is less than the operand.
     * - Returns self::COMPARE_EQ if both values are equal.
     * - Returns self::COMPARE_GT if this instance is greater than the operand.
     *
     * When comparing with an array of values and mode is self::MODE_ALL:
     * - Returns true only if the comparison is true for all values in the array.
     *
     * When comparing with an array of values and mode is self::MODE_ANY:
     * - Returns true if the comparison is true for at least one value in the array.
     *
     * @param Byte|int|float|string|array $operand The value(s) to compare with.
     * @param string $mode The comparison mode: 'all' or 'any'. Default is 'all'.
     * @return int|bool Comparison result.
     */
    public function compare(Byte|int|float|string|array $operand, string $mode = self::MODE_ALL): int|bool
    {
        if (is_array($operand)) {
            $results = [];
            foreach ($operand as $item) {
                $results[] = $this->compareSingle($item);
            }

            return match ($mode) {
                self::MODE_ALL => count(array_unique($results)) === 1 ? $results[0] : false,
                self::MODE_ANY => in_array(self::COMPARE_EQ, $results) ? self::COMPARE_EQ :
                    (in_array(self::COMPARE_GT, $results) ? self::COMPARE_GT : self::COMPARE_LT),
                default => throw new \InvalidArgumentException("Invalid mode: $mode"),
            };
        }

        return $this->compareSingle($operand);
    }

    /**
     * Compares this Byte instance with a single value.
     *
     * @param Byte|int|float|string $operand The value to compare with.
     * @return int One of the comparison constants.
     */
    private function compareSingle(Byte|int|float|string $operand): int
    {
        $operandValue = self::parse($operand);

        return match (true) {
            $operandValue == $this->value => self::COMPARE_EQ,
            $this->value > $operandValue => self::COMPARE_GT,
            default => self::COMPARE_LT
        };
    }

    /**
     * Checks if the current Byte value is equal to a given operand or all/any operands in an array.
     *
     * @param Byte|int|float|string|array $operand The value(s) to compare with.
     * @param string $mode The comparison mode: 'all' or 'any'. Default is 'all'.
     * @return bool True if the values are equal according to the specified mode.
     */
    public function equalTo(Byte|int|float|string|array $operand, string $mode = self::MODE_ALL): bool
    {
        if (is_array($operand)) {
            $results = [];
            foreach ($operand as $item) {
                $results[] = self::parse($item) == $this->value;
            }

            return match ($mode) {
                self::MODE_ALL => !in_array(false, $results),
                self::MODE_ANY => in_array(true, $results),
                default => throw new \InvalidArgumentException("Invalid mode: $mode"),
            };
        }

        return self::parse($operand) == $this->value;
    }

    /**
     * Checks if the current Byte value is less than a given operand or all/any operands in an array.
     *
     * @param Byte|int|float|string|array $operand The value(s) to compare with.
     * @param string $mode The comparison mode: 'all' or 'any'. Default is 'all'.
     * @return bool True if current value is less than the operand(s) according to the specified mode.
     */
    public function lessThan(Byte|int|float|string|array $operand, string $mode = self::MODE_ALL): bool
    {
        if (is_array($operand)) {
            $results = [];
            foreach ($operand as $item) {
                $results[] = self::parse($item) > $this->value;
            }

            return match ($mode) {
                self::MODE_ALL => !in_array(false, $results),
                self::MODE_ANY => in_array(true, $results),
                default => throw new \InvalidArgumentException("Invalid mode: $mode"),
            };
        }

        return self::parse($operand) > $this->value;
    }

    /**
     * Checks if the current Byte value is greater than a given operand or all/any operands in an array.
     *
     * @param Byte|int|float|string|array $operand The value(s) to compare with.
     * @param string $mode The comparison mode: 'all' or 'any'. Default is 'all'.
     * @return bool True if current value is greater than the operand(s) according to the specified mode.
     */
    public function greaterThan(Byte|int|float|string|array $operand, string $mode = self::MODE_ALL): bool
    {
        if (is_array($operand)) {
            $results = [];
            foreach ($operand as $item) {
                $results[] = $this->value > self::parse($item);
            }

            return match ($mode) {
                self::MODE_ALL => !in_array(false, $results),
                self::MODE_ANY => in_array(true, $results),
                default => throw new \InvalidArgumentException("Invalid mode: $mode"),
            };
        }

        return $this->value > self::parse($operand);
    }

    /**
     * Checks if the current Byte value is less than or equal to a given operand or all/any operands in an array.
     *
     * @param Byte|int|float|string|array $operand The value(s) to compare with.
     * @param string $mode The comparison mode: 'all' or 'any'. Default is 'all'.
     * @return bool True if current value is less than or equal to the operand(s) according to the specified mode.
     */
    public function lessThanOrEqual(Byte|int|float|string|array $operand, string $mode = self::MODE_ALL): bool
    {
        if (is_array($operand)) {
            $results = [];
            foreach ($operand as $item) {
                $results[] = self::parse($item) >= $this->value;
            }

            return match ($mode) {
                self::MODE_ALL => !in_array(false, $results),
                self::MODE_ANY => in_array(true, $results),
                default => throw new \InvalidArgumentException("Invalid mode: $mode"),
            };
        }

        return self::parse($operand) >= $this->value;
    }

    /**
     * Checks if the current Byte value is greater than or equal to a given operand or all/any operands in an array.
     *
     * @param Byte|int|float|string|array $operand The value(s) to compare with.
     * @param string $mode The comparison mode: 'all' or 'any'. Default is 'all'.
     * @return bool True if current value is greater than or equal to the operand(s) according to the specified mode.
     */
    public function greaterThanOrEqual(Byte|int|float|string|array $operand, string $mode = self::MODE_ALL): bool
    {
        if (is_array($operand)) {
            $results = [];
            foreach ($operand as $item) {
                $results[] = $this->value >= self::parse($item);
            }

            return match ($mode) {
                self::MODE_ALL => !in_array(false, $results),
                self::MODE_ANY => in_array(true, $results),
                default => throw new \InvalidArgumentException("Invalid mode: $mode"),
            };
        }

        return $this->value >= self::parse($operand);
    }

    /**
     * Checks if the current Byte value is between two values (inclusive).
     *
     * @param Byte|int|float|string $min The minimum value.
     * @param Byte|int|float|string $max The maximum value.
     * @return bool True if the current value is between min and max (inclusive).
     */
    public function between(Byte|int|float|string $min, Byte|int|float|string $max): bool
    {
        $minValue = self::parse($min);
        $maxValue = self::parse($max);

        return $this->value >= $minValue && $this->value <= $maxValue;
    }

    /**
     * Increments the current Byte value by the given operand.
     *
     * @param Byte|int|float|string $value The value to add.
     * @return static A new Byte instance with the incremented value.
     */
    public function increment(Byte|int|float|string $value): static
    {
        return new static($this->value + self::parse($value));
    }

    /**
     * Decrements the current Byte value by the given operand.
     *
     * @param Byte|int|float|string $value The value to subtract.
     * @return static A new Byte instance with the decremented value.
     * @throws \LogicException If the operand is greater than the current value.
     */
    public function decrement(Byte|int|float|string $value): static
    {
        $parsedValue = self::parse($value);
        if ($parsedValue > $this->value) {
            throw new \LogicException("Value to decrement ($parsedValue) cannot be greater than the current value ({$this->value})");
        }

        return new static($this->value - $parsedValue);
    }

    /**
     * Divides the current Byte value by the given operand.
     *
     * @param Byte|int|float|string $value The value to divide by.
     * @return static A new Byte instance with the divided value.
     * @throws \DivisionByZeroError If the divisor is zero.
     */
    public function divide(Byte|int|float|string $value): static
    {
        $parsedValue = self::parse($value);
        if ($parsedValue == 0) {
            throw new \DivisionByZeroError("Cannot divide by zero");
        }

        return new static($this->value / $parsedValue);
    }

    /**
     * Multiplies the current Byte value by the given operand.
     *
     * @param int|float $value The multiplier.
     * @return static A new Byte instance with the multiplied value.
     */
    public function multiply(int|float $value): static
    {
        return new static($this->value * $value);
    }

    /**
     * Takes the modulo of the current Byte value with the given operand.
     *
     * @param Byte|int|float|string $value The value to take modulo with.
     * @return static A new Byte instance with the modulo result.
     * @throws \DivisionByZeroError If the divisor is zero.
     */
    public function modulo(Byte|int|float|string $value): static
    {
        $parsedValue = self::parse($value);
        if ($parsedValue == 0) {
            throw new \DivisionByZeroError("Cannot take modulo with zero");
        }

        return new static($this->value % $parsedValue);
    }

    /**
     * Determines if the current Byte value is zero.
     *
     * @return bool True if the value is zero, false otherwise.
     */
    public function isZero(): bool
    {
        return $this->value === 0;
    }

    /**
     * Determines if the current Byte value is positive.
     *
     * @return bool True if the value is positive, false otherwise.
     */
    public function isPositive(): bool
    {
        return $this->value > 0;
    }

    /**
     * Determines if the current Byte value is negative.
     *
     * @return bool True if the value is negative, false otherwise.
     */
    public function isNegative(): bool
    {
        return $this->value < 0;
    }

    /**
     * Returns the absolute value of the current Byte value.
     *
     * @return static A new Byte instance with the absolute value.
     */
    public function abs(): static
    {
        return new static(abs($this->value));
    }

    /**
     * Finds the maximum of the current Byte value and the given operands.
     *
     * @param Byte|int|float|string|array $values The value(s) to compare with.
     * @return static A new Byte instance with the maximum value.
     */
    public function max(Byte|int|float|string|array $values): static
    {
        $maxValue = $this->value;

        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $value) {
            $parsedValue = self::parse($value);
            $maxValue = max($maxValue, $parsedValue);
        }

        return new static($maxValue);
    }

    /**
     * Finds the minimum of the current Byte value and the given operands.
     *
     * @param Byte|int|float|string|array $values The value(s) to compare with.
     * @return static A new Byte instance with the minimum value.
     */
    public function min(Byte|int|float|string|array $values): static
    {
        $minValue = $this->value;

        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $value) {
            $parsedValue = self::parse($value);
            $minValue = min($minValue, $parsedValue);
        }

        return new static($minValue);
    }

    /**
     * Checks if the current Byte value is within a range of values.
     *
     * @param array $ranges Array of ranges, each a two-element array [min, max]
     * @param string $mode The comparison mode: 'all' or 'any'. Default is 'any'.
     * @return bool True if the current value is within the specified ranges according to the mode.
     */
    public function inRanges(array $ranges, string $mode = self::MODE_ANY): bool
    {
        $results = [];

        foreach ($ranges as $range) {
            if (!is_array($range) || count($range) !== 2) {
                throw new \InvalidArgumentException("Each range must be an array with exactly two elements [min, max]");
            }

            $min = self::parse($range[0]);
            $max = self::parse($range[1]);

            $results[] = $this->value >= $min && $this->value <= $max;
        }

        return match ($mode) {
            self::MODE_ALL => !in_array(false, $results),
            self::MODE_ANY => in_array(true, $results),
            default => throw new \InvalidArgumentException("Invalid mode: $mode"),
        };
    }

    /**
     * Converts the given byte numeric value to a human-readable string with appropriate units.
     *
     * Iterates over supported units and computes the value in that unit.
     *
     * @param int|float $bytes The number of bytes.
     * @param int $precision The number of decimal places to round the result.
     * @param string $delim The delimiter between the number and the unit.
     * @return string A formatted string representing the bytes in the most suitable unit.
     */
    public static function humanize(int|float $bytes, int $precision = 2, string $delim = ' '): string
    {
        foreach (self::units as $unit => $exponent) {
            if (($result = $bytes / pow(self::amount, $exponent)) < 1) continue;
            if ($precision) $result = round($result, $precision);
            return "$result$delim$unit";
        }

        // If all conversions result in < 1, return bytes
        return "$bytes{$delim}B";
    }

    /**
     * Parses a given value (Byte instance, numeric value, or string) into a numeric byte value.
     *
     * For numeric input, it returns the numeric value.
     * For a Byte instance, it returns its stored value.
     * For a string, it expects the string to consist of a numeric part followed by a valid two-character unit.
     *
     * Supported units are defined in the self::units array (e.g., "B", "kB", "MB", etc.).
     *
     * @param Byte|int|float|string $value The value to parse.
     * @return int|float The parsed numeric byte value.
     *
     * @throws \InvalidArgumentException If the input is a string that does not contain a valid numeric part or if the provided unit is not recognized.
     */
    public static function parse(self|int|float|string $value): int|float
    {
        // If the value is numeric, simply return it as an integer or float.
        if (is_numeric($value)) return $value + 0;

        // If the value is already a Byte instance, use its stored byte value.
        if ($value instanceof self) return $value->value;

        // Trim the input string to handle extra whitespace
        $value = trim($value);

        // Match pattern: number followed by optional whitespace followed by unit
        if (!preg_match('/^(\d+(?:\.\d+)?)\s*([a-zA-Z]{1,2})$/', $value, $matches)) {
            throw new \InvalidArgumentException('Failed to parse string: The format is invalid.');
        }

        $bytes = $matches[1];
        $units = $matches[2];

        foreach (self::units as $unit => $exponent) {
            if (strcasecmp($unit, $units) === 0) {
                return $bytes * pow(self::amount, $exponent);
            }
        }

        throw new \InvalidArgumentException("Failed to parse string: Unrecognized unit '{$units}'.");
    }

    /**
     * Creates an array of Byte instances within a specified range.
     *
     * @param Byte|int|float|string $start The starting value of the range.
     * @param Byte|int|float|string $end The ending value of the range.
     * @param Byte|int|float|string $step The step value for increments. Default is 1B.
     * @return array An array of Byte instances.
     * @throws \InvalidArgumentException If end < start or step <= 0.
     */
    public static function range(Byte|int|float|string $start, Byte|int|float|string $end, Byte|int|float|string $step = 1): array
    {
        $startValue = self::parse($start);
        $endValue = self::parse($end);
        $stepValue = self::parse($step);

        if ($endValue < $startValue) {
            throw new \InvalidArgumentException("End value cannot be less than start value");
        }

        if ($stepValue <= 0) {
            throw new \InvalidArgumentException("Step value must be greater than zero");
        }

        $result = [];
        $current = $startValue;

        while ($current <= $endValue) {
            $result[] = new self($current);
            $current += $stepValue;
        }

        return $result;
    }

    /**
     * Creates a Byte object from a human-readable size string.
     *
     * @param string $sizeString A string like "5 MB", "1.5GB", etc.
     * @return static A new Byte instance.
     * @throws \InvalidArgumentException If the string format is invalid.
     */
    public static function fromHumanReadable(string $sizeString): static
    {
        return new static($sizeString);
    }

    /**
     * Sums an array of byte values.
     *
     * @param array $bytes Array of Byte|int|float|string values.
     * @return static A new Byte instance with the sum.
     */
    public static function sum(array $bytes): static
    {
        $total = 0;

        foreach ($bytes as $byte) {
            $total += self::parse($byte);
        }

        return new static($total);
    }

    /**
     * Finds the average of an array of byte values.
     *
     * @param array $bytes Array of Byte|int|float|string values.
     * @return static A new Byte instance with the average.
     * @throws \InvalidArgumentException If the array is empty.
     */
    public static function average(array $bytes): static
    {
        if (empty($bytes)) {
            throw new \InvalidArgumentException("Cannot compute average of an empty array");
        }

        return self::sum($bytes)->divide(count($bytes));
    }

    /**
     * Finds the maximum value in an array of byte values.
     *
     * @param array $bytes Array of Byte|int|float|string values.
     * @return static A new Byte instance with the maximum value.
     * @throws \InvalidArgumentException If the array is empty.
     */
    public static function maximum(array $bytes): static
    {
        if (empty($bytes)) {
            throw new \InvalidArgumentException("Cannot find maximum of an empty array");
        }

        $max = self::parse($bytes[0]);

        foreach ($bytes as $byte) {
            $value = self::parse($byte);
            if ($value > $max) {
                $max = $value;
            }
        }

        return new static($max);
    }

    /**
     * Finds the minimum value in an array of byte values.
     *
     * @param array $bytes Array of Byte|int|float|string values.
     * @return static A new Byte instance with the minimum value.
     * @throws \InvalidArgumentException If the array is empty.
     */
    public static function minimum(array $bytes): static
    {
        if (empty($bytes)) {
            throw new \InvalidArgumentException("Cannot find minimum of an empty array");
        }

        $min = self::parse($bytes[0]);

        foreach ($bytes as $byte) {
            $value = self::parse($byte);
            if ($value < $min) {
                $min = $value;
            }
        }

        return new static($min);
    }

    /**
     * Converts bytes to bits.
     *
     * @return float The value in bits.
     */
    public function toBits(): float
    {
        return $this->value * 8;
    }

    /**
     * Creates a Byte instance from a bit value.
     *
     * @param int|float $bits The number of bits.
     * @return static A new Byte instance.
     */
    public static function fromBits(int|float $bits): static
    {
        return new static($bits / 8);
    }

    /**
     * Calculates the transfer time in seconds based on a bandwidth.
     *
     * @param Byte|int|float|string $bandwidthPerSecond The bandwidth in bytes per second.
     * @return float The time in seconds.
     * @throws \InvalidArgumentException If bandwidth is zero or negative.
     */
    public function getTransferTime(Byte|int|float|string $bandwidthPerSecond): float
    {
        $bandwidth = self::parse($bandwidthPerSecond);

        if ($bandwidth <= 0) {
            throw new \InvalidArgumentException("Bandwidth must be positive");
        }

        return $this->value / $bandwidth;
    }

    /**
     * Formats the transfer time into a human-readable string.
     *
     * @param Byte|int|float|string $bandwidthPerSecond The bandwidth in bytes per second.
     * @return string A formatted string like "5 minutes, 30 seconds".
     */
    public function getFormattedTransferTime(Byte|int|float|string $bandwidthPerSecond): string
    {
        $seconds = $this->getTransferTime($bandwidthPerSecond);

        if ($seconds < 1) {
            return "less than a second";
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        if ($minutes < 1) {
            return round($remainingSeconds) . " second" . ($remainingSeconds == 1 ? "" : "s");
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours < 1) {
            $result = $remainingMinutes . " minute" . ($remainingMinutes == 1 ? "" : "s");
            if ($remainingSeconds > 0) {
                $result .= ", " . round($remainingSeconds) . " second" . ($remainingSeconds == 1 ? "" : "s");
            }
            return $result;
        }

        $days = floor($hours / 24);
        $remainingHours = $hours % 24;

        if ($days < 1) {
            $result = $remainingHours . " hour" . ($remainingHours == 1 ? "" : "s");
            if ($remainingMinutes > 0) {
                $result .= ", " . $remainingMinutes . " minute" . ($remainingMinutes == 1 ? "" : "s");
            }
            return $result;
        }

        $result = $days . " day" . ($days == 1 ? "" : "s");
        if ($remainingHours > 0) {
            $result .= ", " . $remainingHours . " hour" . ($remainingHours == 1 ? "" : "s");
        }
        return $result;
    }
}
