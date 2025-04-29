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
    public const COMPARE_LT = -1;
    public const COMPARE_EQ = 0;
    public const COMPARE_GT = 1;

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
        return new self($value*pow(self::amount, self::units['KB']));
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
                if ($precision) return round($this->value / pow(self::amount, $exponent), $precision)
                    . "$delim$unit";

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
     * Compares this Byte instance with another value.
     *
     * Returns:
     * - self::COMPARE_LT if this instance is less than the operand.
     * - self::COMPARE_EQ if both values are equal.
     * - self::COMPARE_GT if this instance is greater than the operand.
     *
     * @param Byte|int|float|string $operand The value to compare with.
     * @return int One of the comparison constants.
     */
    public function compare(self|int|float|string $operand): int
    {
        return match (true) {
            ($operand = self::parse($operand)) == $this->value => self::COMPARE_EQ,
            $this->value > $operand => self::COMPARE_GT,
            default => self::COMPARE_LT
        };
    }

    /**
     * Increments the current Byte value by the given operand.
     *
     * @param Byte|int|float|string $value The value to add.
     * @return $this A new Byte instance with the incremented value.
     */
    public function increment(self|int|float|string $value): self
    {
        return new self($this->value + self::parse($value));
    }

    /**
     * Decrements the current Byte value by the given operand.
     *
     * @param Byte|int|float|string $value The value to subtract.
     * @return $this A new Byte instance with the decremented value.
     * @throws \LogicException If the operand is greater than the current value.
     */
    public function decrement(self|int|float|string $value): self
    {
        if (($value = self::parse($value)) > $this->value) {
            throw new \LogicException('[$value] can not be greater than '. $this->value);
        }

        return new self($this->value - $value);
    }

    /**
     * Checks if the current Byte value is equal to the given operand.
     *
     * @param Byte|int|float|string $operand The value to compare with.
     * @return bool True if the values are equal, false otherwise.
     */
    public function equalTo(self|int|float|string $operand): bool
    {
        return self::parse($operand) == $this->value;
    }

    /**
     * Divides the current Byte value by the given operand.
     *
     * @param Byte|int|float|string $value The value to divide by.
     * @return self A new Byte instance with the divided value.
     * @throws \LogicException If the operand is greater than the current value.
     */
    public function divide(self|int|float|string $value): self
    {
        if (($value = self::parse($value)) > $this->value) {
            throw new \LogicException('[$value] can not be greater than '. $this->value);
        }

        return new self($this->value - $value);
    }

    /**
     * Multiplies the current Byte value by the given operand.
     *
     * @param Byte|int|float|string $value The multiplier.
     * @return self A new Byte instance with the multiplied value.
     */
    public function multiply(self|int|float|string $value): self
    {
        return new self($this->value * $value);
    }

    /**
     * Returns true if the current Byte value is less than the given operand.
     *
     * @param Byte|int|float|string $operand The value to compare with.
     * @return bool True if current value is less than the operand.
     */
    public function lessThan(self|int|float|string $operand): bool
    {
        return self::parse($operand) > $this->value;
    }

    /**
     * Returns true if the current Byte value is greater than the given operand.
     *
     * @param int|float|string $operand The value to compare with.
     * @return bool True if current value is greater than the operand.
     */
    public function greaterThan(int|float|string $operand): bool
    {
        return $this->value > self::parse($operand);
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
     *
     * Error Description:
     * The method expects a string in the format "[number][space][unit]" (for example, "1024 kB") or a similar valid variation.
     * If the numeric part is not valid or the unit is not found within the supported units, the method throws an InvalidArgumentException
     * with the message "Failed to parse string". This error indicates that the input value does not conform to an expected byte format.
     */
    public static function parse(self|string $value): int|float
    {
        // If the value is numeric, simply return it as an integer or float.
        if (is_numeric($value)) return $value + 0;

        // If the value is already a Byte instance, use its stored byte value.
        if ($value instanceof self) return $value->value;

        // Assume the string format ends with a two-character unit (e.g., "MB", "GB").
        $bytes = trim(substr($value, 0, -2));
        $units = trim(substr($value, -2, 2));

        // If the numeric part is not valid, or if the unit is not recognized, throw an exception.
        if (!is_numeric($bytes)) {
            throw new \InvalidArgumentException('Failed to parse string: The numeric portion is invalid.');
        }

        foreach (self::units as $unit => $exponent) {
            if (strcasecmp($unit, $units) === 0) return $bytes * pow(self::amount, $exponent);
        }

        throw new \InvalidArgumentException('Failed to parse string: Unrecognized unit.');
    }
}
