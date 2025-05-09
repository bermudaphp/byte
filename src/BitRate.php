<?php

namespace Bermuda\Stdlib;

/**
 * Class BitRate
 *
 * Represents a data transfer rate (bit rate or byte rate) and provides methods for
 * converting between different units (bps, kbps, Mbps, Gbps, Bps, kBps, MBps, GBps, etc.).
 *
 * This class can handle both bit-based units (bps, kbps) and byte-based units (Bps, MBps)
 * with automatic conversion between them.
 */
final class BitRate implements \Stringable
{
    public const int COMPARE_LT = -1;
    public const int COMPARE_EQ = 0;
    public const int COMPARE_GT = 1;

    public const string MODE_ALL = 'all';
    public const string MODE_ANY = 'any';

    /**
     * The value in bits per second.
     */
    public readonly int|float $value;

    /**
     * Flag indicating whether the default representation is in bits or bytes.
     */
    private bool $displayAsBits;

    /**
     * Mapping of units to their corresponding exponential factors.
     * For example, 'Gbps' corresponds to 3 (i.e. 1000^3 bits per second).
     *
     * @var array<string, array<string, int>>
     */
    private static array $rateUnits = [
        'bit' => [
            'Ybps' => 8, 'Zbps' => 7, 'Ebps' => 6,
            'Pbps' => 5, 'Tbps' => 4, 'Gbps' => 3,
            'Mbps' => 2, 'kbps' => 1, 'bps' => 0
        ],
        'byte' => [
            'YBps' => 8, 'ZBps' => 7, 'EBps' => 6,
            'PBps' => 5, 'TBps' => 4, 'GBps' => 3,
            'MBps' => 2, 'kBps' => 1, 'Bps' => 0
        ]
    ];

    /**
     * The multiplier used between units.
     */
    private static int $rateMultiplier = 1000;

    /**
     * BitRate constructor.
     *
     * @param int|float|string|self $value The bit rate value (in bits per second if numeric,
     *                                      or as a formatted string)
     * @param bool $isBits Whether the value provided is in bits (true) or bytes (false)
     * @param bool $displayAsBits Whether to display the rate as bits (true) or bytes (false) by default
     */
    public function __construct(int|float|string|self $value, bool $isBits = true, bool $displayAsBits = true)
    {
        $this->value = $this->parseRate($value, $isBits);
        $this->displayAsBits = $displayAsBits;
    }

    /**
     * Creates a new BitRate instance.
     *
     * @param int|float|string|self $value The rate value to use
     * @param bool $isBits Whether the value provided is in bits (true) or bytes (false)
     * @param bool $displayAsBits Whether to display the rate as bits or bytes by default
     * @return static A new BitRate instance
     */
    public static function new(int|float|string|self $value, bool $isBits = true, bool $displayAsBits = true): self
    {
        return new self($value, $isBits, $displayAsBits);
    }

    /**
     * Creates a BitRate instance from bits per second (bps).
     *
     * @param int|float $value The number of bits per second
     * @param bool $displayAsBits Whether to display the rate as bits (true) or bytes (false) by default
     * @return static A new BitRate instance
     */
    public static function bps(int|float $value, bool $displayAsBits = true): self
    {
        return new self($value, true, $displayAsBits);
    }

    /**
     * Creates a BitRate instance from kilobits per second (kbps).
     *
     * @param int|float $value The number of kilobits per second
     * @param bool $displayAsBits Whether to display the rate as bits (true) or bytes (false) by default
     * @return static A new BitRate instance
     */
    public static function kbps(int|float $value, bool $displayAsBits = true): self
    {
        return new self($value * pow(self::$rateMultiplier, self::$rateUnits['bit']['kbps']), true, $displayAsBits);
    }

    /**
     * Creates a BitRate instance from megabits per second (Mbps).
     *
     * @param int|float $value The number of megabits per second
     * @param bool $displayAsBits Whether to display the rate as bits (true) or bytes (false) by default
     * @return static A new BitRate instance
     */
    public static function mbps(int|float $value, bool $displayAsBits = true): self
    {
        return new self($value * pow(self::$rateMultiplier, self::$rateUnits['bit']['Mbps']), true, $displayAsBits);
    }

    /**
     * Creates a BitRate instance from gigabits per second (Gbps).
     *
     * @param int|float $value The number of gigabits per second
     * @param bool $displayAsBits Whether to display the rate as bits (true) or bytes (false) by default
     * @return static A new BitRate instance
     */
    public static function gbps(int|float $value, bool $displayAsBits = true): self
    {
        return new self($value * pow(self::$rateMultiplier, self::$rateUnits['bit']['Gbps']), true, $displayAsBits);
    }

    /**
     * Creates a BitRate instance from terabits per second (Tbps).
     *
     * @param int|float $value The number of terabits per second
     * @param bool $displayAsBits Whether to display the rate as bits (true) or bytes (false) by default
     * @return static A new BitRate instance
     */
    public static function tbps(int|float $value, bool $displayAsBits = true): self
    {
        return new self($value * pow(self::$rateMultiplier, self::$rateUnits['bit']['Tbps']), true, $displayAsBits);
    }

    /**
     * Creates a BitRate instance from bytes per second (Bps).
     *
     * @param int|float $value The number of bytes per second
     * @param bool $displayAsBits Whether to display the rate as bits (true) or bytes (false) by default
     * @return static A new BitRate instance
     */
    public static function bps_bytes(int|float $value, bool $displayAsBits = false): self
    {
        return new self($value, false, $displayAsBits);
    }

    /**
     * Creates a BitRate instance from kilobytes per second (kBps).
     *
     * @param int|float $value The number of kilobytes per second
     * @param bool $displayAsBits Whether to display the rate as bits (true) or bytes (false) by default
     * @return static A new BitRate instance
     */
    public static function kbps_bytes(int|float $value, bool $displayAsBits = false): self
    {
        return new self($value * pow(self::$rateMultiplier, self::$rateUnits['byte']['kBps']), false, $displayAsBits);
    }

    /**
     * Creates a BitRate instance from megabytes per second (MBps).
     *
     * @param int|float $value The number of megabytes per second
     * @param bool $displayAsBits Whether to display the rate as bits (true) or bytes (false) by default
     * @return static A new BitRate instance
     */
    public static function mbps_bytes(int|float $value, bool $displayAsBits = false): self
    {
        return new self($value * pow(self::$rateMultiplier, self::$rateUnits['byte']['MBps']), false, $displayAsBits);
    }

    /**
     * Creates a BitRate instance from gigabytes per second (GBps).
     *
     * @param int|float $value The number of gigabytes per second
     * @param bool $displayAsBits Whether to display the rate as bits (true) or bytes (false) by default
     * @return static A new BitRate instance
     */
    public static function gbps_bytes(int|float $value, bool $displayAsBits = false): self
    {
        return new self($value * pow(self::$rateMultiplier, self::$rateUnits['byte']['GBps']), false, $displayAsBits);
    }

    /**
     * Creates BitRate instances from values in bytes per second for easier readability
     *
     * Alias methods for byte-based rates
     */
    public static function bps_b(int|float $value): self { return self::bps_bytes($value); }
    public static function kbps_b(int|float $value): self { return self::kbps_bytes($value); }
    public static function mbps_b(int|float $value): self { return self::mbps_bytes($value); }
    public static function gbps_b(int|float $value): self { return self::gbps_bytes($value); }

    /**
     * Creates a BitRate instance from a value in any supported unit.
     *
     * @param int|float $value The numeric value
     * @param string $unit The unit (bps, kbps, Mbps, etc.)
     * @param bool $displayAsBits Whether to display the rate as bits or bytes by default
     * @return static A new BitRate instance
     * @throws \InvalidArgumentException If the unit is not supported
     */
    public static function from(int|float $value, string $unit, bool $displayAsBits = null): self
    {
        $unit = strtolower($unit);
        $isBit = true;
        $displayDefault = true;

        // Check byte units first
        foreach (self::$rateUnits['byte'] as $supportedUnit => $exponent) {
            if (strcasecmp($unit, $supportedUnit) === 0) {
                $isBit = false;
                $displayDefault = false;
                $bitsValue = $value * pow(self::$rateMultiplier, $exponent) * 8; // Convert to bits
                return new self($bitsValue, true, $displayAsBits ?? $displayDefault);
            }
        }

        // Then check bit units
        foreach (self::$rateUnits['bit'] as $supportedUnit => $exponent) {
            if (strcasecmp($unit, $supportedUnit) === 0) {
                $bitsValue = $value * pow(self::$rateMultiplier, $exponent);
                return new self($bitsValue, true, $displayAsBits ?? $displayDefault);
            }
        }

        throw new \InvalidArgumentException("Unsupported unit: $unit");
    }

    /**
     * Parses a rate string (e.g., "5 Mbps" or "10 MBps") into its numeric bits per second value.
     *
     * @param mixed $value The value to parse
     * @param bool $isBits Whether the value should be interpreted as bits (true) or bytes (false)
     * @return int|float The parsed numeric value in bits per second
     */
    protected function parseRate(mixed $value, bool $isBits): int|float
    {
        if (is_numeric($value)) {
            // If input is numeric, convert to bits if it's bytes
            return $isBits ? ($value + 0) : (($value + 0) * 8);
        }

        if ($value instanceof self) {
            return $value->value; // BitRate already stores value in bits per second
        }

        // For string values, parse according to the unit
        $value = trim($value);

        // Match pattern: number followed by optional whitespace followed by unit
        if (!preg_match('/^(\d+(?:\.\d+)?)\s*([a-zA-Z]{3,4})$/', $value, $matches)) {
            throw new \InvalidArgumentException('Failed to parse rate string: The format is invalid.');
        }

        $amount = $matches[1];
        $units = $matches[2];

        // Check byte units first
        foreach (self::$rateUnits['byte'] as $unit => $exponent) {
            if (strcasecmp($unit, $units) === 0) {
                return $amount * pow(self::$rateMultiplier, $exponent) * 8; // Convert to bits
            }
        }

        // Then check bit units
        foreach (self::$rateUnits['bit'] as $unit => $exponent) {
            if (strcasecmp($unit, $units) === 0) {
                return $amount * pow(self::$rateMultiplier, $exponent);
            }
        }

        throw new \InvalidArgumentException("Failed to parse rate string: Unrecognized unit '{$units}'.");
    }

    /**
     * Gets the rate value in bits per second.
     *
     * @return float|int The value in bits per second
     */
    public function toBits(): float|int
    {
        return $this->value; // Already stored in bits per second
    }

    /**
     * Gets the rate value in bytes per second.
     *
     * @return float|int The value in bytes per second
     */
    public function toBytes(): float|int
    {
        return $this->value / 8; // Convert bits to bytes
    }

    /**
     * Converts the rate value to a specified unit and returns a formatted string.
     *
     * @param string $units The target unit (e.g., 'Mbps', 'GBps')
     * @param int|null $precision The number of decimals to round to
     * @param string $delim A delimiter between the value and unit
     * @return string The formatted string
     * @throws \InvalidArgumentException If the unit is not supported
     */
    public function to(string $units, ?int $precision = null, string $delim = ' '): string
    {
        $type = $this->getUnitType($units);

        if ($type === null) {
            throw new \InvalidArgumentException("Unsupported unit: $units");
        }

        $baseValue = ($type === 'bit') ? $this->value : $this->toBytes();

        foreach (self::$rateUnits[$type] as $unit => $exponent) {
            if (strcasecmp($units, $unit) === 0) {
                $convertedValue = $baseValue / pow(self::$rateMultiplier, $exponent);

                if ($precision !== null) {
                    return round($convertedValue, $precision) . "$delim$unit";
                }

                return $convertedValue . "$delim$unit";
            }
        }

        throw new \InvalidArgumentException("Unsupported unit: $units");
    }

    /**
     * Gets the numeric value in a specific rate unit.
     *
     * @param string $unit The unit to convert to
     * @param int|null $precision Precision for rounding
     * @return float|int The value in the specified unit
     * @throws \InvalidArgumentException If the unit is not supported
     */
    public function getRateValue(string $unit, ?int $precision = null): float|int
    {
        if ($unit === 'bit') {
            return $this->toBits();
        }

        if ($unit === 'byte') {
            return $this->toBytes();
        }

        $type = $this->getUnitType($unit);

        if ($type === null) {
            throw new \InvalidArgumentException("Unsupported unit: $unit");
        }

        $baseValue = ($type === 'bit') ? $this->value : $this->toBytes();

        foreach (self::$rateUnits[$type] as $unitKey => $exponent) {
            if (strcasecmp($unit, $unitKey) === 0) {
                $value = $baseValue / pow(self::$rateMultiplier, $exponent);
                return $precision !== null ? round($value, $precision) : $value;
            }
        }

        throw new \InvalidArgumentException("Unsupported unit: $unit");
    }

    /**
     * Determines the type (bit or byte) of a given unit.
     *
     * @param string $unit The unit to check
     * @return string|null 'bit', 'byte', or null if not found
     */
    private function getUnitType(string $unit): ?string
    {
        foreach (self::$rateUnits as $type => $units) {
            foreach ($units as $unitKey => $exponent) {
                if (strcasecmp($unit, $unitKey) === 0) {
                    return $type;
                }
            }
        }

        return null;
    }

    /**
     * Convenience methods for commonly used unit conversions (bits)
     */
    public function toKbps(?int $precision = null, string $delim = ' '): string { return $this->to('kbps', $precision, $delim); }
    public function toMbps(?int $precision = null, string $delim = ' '): string { return $this->to('Mbps', $precision, $delim); }
    public function toGbps(?int $precision = null, string $delim = ' '): string { return $this->to('Gbps', $precision, $delim); }
    public function toTbps(?int $precision = null, string $delim = ' '): string { return $this->to('Tbps', $precision, $delim); }

    /**
     * String representation of the BitRate.
     *
     * @return string Human-readable representation
     */
    public function __toString(): string
    {
        return $this->toString($this->displayAsBits ? 'bit' : 'byte');
    }

    /**
     * Humanized string representation of the BitRate.
     *
     * @param string $type The unit type to use ('bit' or 'byte')
     * @param int $precision The precision for rounding
     * @param string $delim The delimiter between value and unit
     * @return string Human-readable representation
     */
    public function toString(string $type = null, int $precision = 2, string $delim = ' '): string
    {
        $type = $type ?? ($this->displayAsBits ? 'bit' : 'byte');
        return $this->humanizeRate($this->value, $type, $precision, $delim);
    }

    /**
     * Gets a copy of this BitRate with the display preference changed.
     *
     * @param bool $displayAsBits Whether to display as bits (true) or bytes (false)
     * @return static A new BitRate instance with the same value but different display preference
     */
    public function withDisplayAs(bool $displayAsBits): static
    {
        $clone = new static($this->value, true, $displayAsBits);
        return $clone;
    }

    /**
     * Humanizes a rate value to a readable string with appropriate units.
     *
     * @param int|float $value The value to humanize (in bits per second)
     * @param string $type The type of unit to use ('bit' or 'byte')
     * @param int $precision The precision for rounding
     * @param string $delim The delimiter between value and unit
     * @return string The humanized string
     */
    protected function humanizeRate(int|float $value, string $type = 'bit', int $precision = 2, string $delim = ' '): string
    {
        if (!isset(self::$rateUnits[$type])) {
            throw new \InvalidArgumentException("Invalid type: $type. Must be 'bit' or 'byte'");
        }

        // If we're displaying as bytes, convert from bits
        if ($type === 'byte') {
            $value = $value / 8;
        }

        foreach (self::$rateUnits[$type] as $unit => $exponent) {
            if (($result = $value / pow(self::$rateMultiplier, $exponent)) >= 1) {
                return round($result, $precision) . "$delim$unit";
            }
        }

        // Default to bps or Bps if value is very small
        $baseUnit = $type === 'bit' ? 'bps' : 'Bps';
        return round($value, $precision) . "$delim$baseUnit";
    }

    /**
     * Compares this BitRate with another value.
     *
     * @param BitRate|int|float|string $operand The value to compare with
     * @param string $mode The comparison mode for arrays
     * @return int|bool COMPARE_LT (-1), COMPARE_EQ (0), or COMPARE_GT (1) for single values,
     *                  boolean for array comparisons
     */
    public function compare(mixed $operand, string $mode = self::MODE_ALL): int|bool
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
     * Compares with a single value.
     *
     * @param BitRate|int|float|string $operand The value to compare with
     * @return int COMPARE_LT (-1), COMPARE_EQ (0), or COMPARE_GT (1)
     */
    private function compareSingle(mixed $operand): int
    {
        $operandValue = $this->getOperandValue($operand);

        return match (true) {
            $operandValue == $this->value => self::COMPARE_EQ,
            $this->value > $operandValue => self::COMPARE_GT,
            default => self::COMPARE_LT
        };
    }

    /**
     * Gets the bit rate value of an operand.
     *
     * @param BitRate|int|float|string $operand The operand
     * @return int|float The bit rate value
     */
    private function getOperandValue(mixed $operand): int|float
    {
        if ($operand instanceof BitRate) {
            return $operand->value;
        }

        // Default to parsing as bits
        return $this->parseRate($operand, true);
    }

    /**
     * Comparison convenience methods
     */
    public function equalTo(mixed $operand, string $mode = self::MODE_ALL): bool
    {
        if (is_array($operand)) {
            $results = [];
            foreach ($operand as $item) {
                $results[] = $this->getOperandValue($item) == $this->value;
            }

            return match ($mode) {
                self::MODE_ALL => !in_array(false, $results),
                self::MODE_ANY => in_array(true, $results),
                default => throw new \InvalidArgumentException("Invalid mode: $mode"),
            };
        }

        return $this->getOperandValue($operand) == $this->value;
    }

    public function lessThan(mixed $operand, string $mode = self::MODE_ALL): bool
    {
        if (is_array($operand)) {
            $results = [];
            foreach ($operand as $item) {
                $results[] = $this->getOperandValue($item) > $this->value;
            }

            return match ($mode) {
                self::MODE_ALL => !in_array(false, $results),
                self::MODE_ANY => in_array(true, $results),
                default => throw new \InvalidArgumentException("Invalid mode: $mode"),
            };
        }

        return $this->getOperandValue($operand) > $this->value;
    }

    public function greaterThan(mixed $operand, string $mode = self::MODE_ALL): bool
    {
        if (is_array($operand)) {
            $results = [];
            foreach ($operand as $item) {
                $results[] = $this->value > $this->getOperandValue($item);
            }

            return match ($mode) {
                self::MODE_ALL => !in_array(false, $results),
                self::MODE_ANY => in_array(true, $results),
                default => throw new \InvalidArgumentException("Invalid mode: $mode"),
            };
        }

        return $this->value > $this->getOperandValue($operand);
    }

    /**
     * Calculates the time required to transfer a given amount of data.
     *
     * @param Byte|int|float|string $dataSize The size of data to transfer
     * @return float The time in seconds
     * @throws \InvalidArgumentException If BitRate is zero
     */
    public function calculateTransferTime(Byte|int|float|string $dataSize): float
    {
        if ($this->value <= 0) {
            throw new \InvalidArgumentException("BitRate must be positive to calculate transfer time");
        }

        $bytes = ($dataSize instanceof Byte) ? $dataSize->value : Byte::parse($dataSize);
        $bits = $bytes * 8;

        return $bits / $this->value;
    }

    /**
     * Formats the transfer time for a given amount of data.
     *
     * @param Byte|int|float|string $dataSize The size of data to transfer
     * @return string A formatted time string (e.g., "5 minutes, 30 seconds")
     */
    public function getFormattedTransferTime(Byte|int|float|string $dataSize): string
    {
        $seconds = $this->calculateTransferTime($dataSize);

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

    /**
     * Calculates how much data can be transferred in a given time period.
     *
     * @param int|float $seconds The time period in seconds
     * @return Byte A Byte object representing the data size
     */
    public function calculateTransferAmount(int|float $seconds): Byte
    {
        $bits = $this->value * $seconds;
        $bytes = $bits / 8;

        return new Byte($bytes);
    }

    /**
     * Arithmetic operations
     */
    public function increment(mixed $value): static
    {
        return new static($this->value + $this->getOperandValue($value), true, $this->displayAsBits);
    }

    public function decrement(mixed $value): static
    {
        $operandValue = $this->getOperandValue($value);

        if ($operandValue > $this->value) {
            throw new \LogicException("Value to decrement ($operandValue) cannot be greater than the current value ({$this->value})");
        }

        return new static($this->value - $operandValue, true, $this->displayAsBits);
    }

    public function multiply(int|float $factor): static
    {
        return new static($this->value * $factor, true, $this->displayAsBits);
    }

    public function divide(int|float $divisor): static
    {
        if ($divisor == 0) {
            throw new \DivisionByZeroError("Cannot divide by zero");
        }

        return new static($this->value / $divisor, true, $this->displayAsBits);
    }

    /**
     * Helper methods
     */
    public function isZero(): bool
    {
        return $this->value === 0;
    }

    public function isPositive(): bool
    {
        return $this->value > 0;
    }

    public function abs(): static
    {
        return new static(abs($this->value), true, $this->displayAsBits);
    }

    /**
     * Finding maximum and minimum values
     */
    public function max(mixed $values): static
    {
        $maxValue = $this->value;

        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $value) {
            $operandValue = $this->getOperandValue($value);
            $maxValue = max($maxValue, $operandValue);
        }

        return new static($maxValue, true, $this->displayAsBits);
    }

    public function min(mixed $values): static
    {
        $minValue = $this->value;

        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $value) {
            $operandValue = $this->getOperandValue($value);
            $minValue = min($minValue, $operandValue);
        }

        return new static($minValue, true, $this->displayAsBits);
    }

    /**
     * Creates a BitRate from a human-readable string.
     *
     * @param string $rateString A string like "5 Mbps", "1.5 GBps", etc.
     * @param bool $displayAsBits Whether to display the rate as bits or bytes by default
     * @return static A new BitRate instance
     */
    public static function fromHumanReadable(string $rateString, bool $displayAsBits = null): static
    {
        if ($displayAsBits === null) {
            // Try to guess the default display mode based on the unit type
            $matches = [];
            if (preg_match('/^(\d+(?:\.\d+)?)\s*([a-zA-Z]{3,4})$/', $rateString, $matches)) {
                $unit = $matches[2];
                $type = null;

                foreach (self::$rateUnits as $unitType => $units) {
                    foreach ($units as $unitKey => $exponent) {
                        if (strcasecmp($unit, $unitKey) === 0) {
                            $type = $unitType;
                            break 2;
                        }
                    }
                }

                $displayAsBits = $type === 'bit';
            } else {
                $displayAsBits = true; // Default to bits if we can't determine
            }
        }

        return new static($rateString, true, $displayAsBits);
    }

    /**
     * Static operations on arrays of rates
     */
    public static function sum(array $rates, bool $displayAsBits = true): static
    {
        $total = 0;
        $instance = new static(0, true, $displayAsBits);

        foreach ($rates as $rate) {
            if ($rate instanceof BitRate) {
                $total += $rate->value;
            } else {
                $total += $instance->getOperandValue($rate);
            }
        }

        return new static($total, true, $displayAsBits);
    }

    public static function average(array $rates, bool $displayAsBits = true): static
    {
        if (empty($rates)) {
            throw new \InvalidArgumentException("Cannot compute average of an empty array");
        }

        return self::sum($rates, $displayAsBits)->divide(count($rates));
    }

    public static function maximum(array $rates, bool $displayAsBits = true): static
    {
        if (empty($rates)) {
            throw new \InvalidArgumentException("Cannot find maximum of an empty array");
        }

        $instance = new static(0, true, $displayAsBits);
        return $instance->max($rates);
    }

    public static function minimum(array $rates, bool $displayAsBits = true): static
    {
        if (empty($rates)) {
            throw new \InvalidArgumentException("Cannot find minimum of an empty array");
        }

        $first = $rates[0];
        $instance = new static($first, true, $displayAsBits);

        return $instance->min(array_slice($rates, 1));
    }

    /**
     * Creates a throttled BitRate based on a factor.
     *
     * @param float $factor The throttle factor (0.0 to 1.0)
     * @return static A new BitRate instance
     * @throws \InvalidArgumentException If factor is outside valid range
     */
    public function throttle(float $factor): static
    {
        if ($factor < 0 || $factor > 1) {
            throw new \InvalidArgumentException("Throttle factor must be between 0 and 1");
        }

        return $this->multiply($factor);
    }

    /**
     * Creates an array of BitRate instances within a specified range.
     *
     * @param BitRate|int|float|string $start The starting value
     * @param BitRate|int|float|string $end The ending value
     * @param BitRate|int|float|string $step The step value (default 1000 bps)
     * @param bool $displayAsBits Whether to display the rates as bits or bytes by default
     * @return array An array of BitRate instances
     * @throws \InvalidArgumentException If end < start or step <= 0
     */
    public static function range(mixed $start, mixed $end, mixed $step = 1000, bool $displayAsBits = true): array
    {
        $instance = new static(0, true, $displayAsBits);
        $startValue = $instance->getOperandValue($start);
        $endValue = $instance->getOperandValue($end);
        $stepValue = $instance->getOperandValue($step);

        if ($endValue < $startValue) {
            throw new \InvalidArgumentException("End value cannot be less than start value");
        }

        if ($stepValue <= 0) {
            throw new \InvalidArgumentException("Step value must be greater than zero");
        }

        $result = [];
        $current = $startValue;

        while ($current <= $endValue) {
            $result[] = new self($current, true, $displayAsBits);
            $current += $stepValue;
        }

        return $result;
    }

    /**
     * Estimates the file size for a duration at this bit rate.
     *
     * @param int $seconds Duration in seconds
     * @return Byte A Byte object representing the estimated size
     */
    public function estimateFileSize(int $seconds): Byte
    {
        $bits = $this->value * $seconds;
        $bytes = $bits / 8;

        return new Byte($bytes);
    }
}
