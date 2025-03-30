<?php

namespace Bermuda\Stdlib;

final class Byte implements \Stringable
{
    public const COMPARE_LT = -1;
    public const COMPARE_EQ = 0;
    public const COMPARE_GT = 1;

    public readonly int|float $value;

    private const units = [
        'YB' => 8, 'ZB' => 7, 'EB' => 6,
        'PB' => 5, 'TB' => 4, 'GB' => 3,
        'MB' => 2, 'kB' => 1, 'B'  => 0
    ];
    
    private const amount = 1024;

    public function __construct(int|float|string $value)
    {
        $this->value = self::parse($value);
    }

    /**
     * @param int|string $value
     * @return static
     */
    public static function new(int|string $value): self
    {
        return new self(is_int($value) ? $value : self::parse($value));
    }

    /**
     * @param int $value
     * @return $this
     */
    public function b(int $value): self
    {
        return new self($value);
    }

    /**
     * @param int $value
     * @return static
     */
    public static function kb(int $value): self
    {
        return new self($value*pow(self::amount, self::units['KB']));
    }

    /**
     * @param int $value
     * @return static
     */
    public static function mb(int $value): self
    {
        return new self($value*pow(self::amount, self::units['MB']));
    }

    /**
     * @param int $value
     * @return static
     */
    public static function gb(int $value): self
    {
        return new self($value*pow(self::amount, self::units['GB']));
    }

    /**
     * @param int $value
     * @return static
     */
    public static function tb(int $value): self
    {
        return new self($value*pow(self::amount, self::units['TB']));
    }

    /**
     * @param int $value
     * @return static
     */
    public static function pb(int $value): self
    {
        return new self($value*pow(self::amount, self::units['PB']));
    }

    /**
     * @param int $value
     * @return static
     */
    public static function eb(int $value): self
    {
        return new self($value*pow(self::amount, self::units['EB']));
    }

    /**
     * @param int $value
     * @return static
     */
    public static function zb(int $value): self
    {
        return new self($value*pow(self::amount, self::units['ZB']));
    }

    /**
     * @param int $value
     * @return static
     */
    public static function yb(int $value): self
    {
        return new self($value*pow(self::amount, self::units['YB']));
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return self::humanize($this->value);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return self::humanize($this->value);
    }

    /**
     * @param string $units
     * @param int|null $precision
     * @param string $delim
     * @return string
     */
    public function to(string $units = 'b', ?int $precision = null, string $delim = ' '): string
    {
        $units = strtolower($units);
        foreach (self::units as $unit => $exponent) {
            if ($units == strtolower($unit)) {
                if ($precision) return round($this->value / pow(self::amount, $exponent), $precision)
                    . "$delim$unit";

                return $this->value / pow(self::amount, $exponent)
                    . "$delim$units";
            }
        }

        return $this->value / self::amount . "{$delim}B";
    }

    public function toKb(?int $precision = null, string $delim = ' '): string
    {
        return $this->to('kb', $precision, $delim);
    }

    public function toMb(?int $precision = null, string $delim = ' '): string
    {
        return $this->to('mb', $precision, $delim);
    }

    public function toGb(?int $precision = null, string $delim = ' '): string
    {
        return $this->to('gb', $precision, $delim);
    }

    public function toTb(?int $precision = null, string $delim = ' '): string
    {
        return $this->to('tb', $precision, $delim);
    }

    public function toPb(?int $precision = null, string $delim = ' '): string
    {
        return $this->to('pb', $precision, $delim);
    }

    public function toEb(?int $precision = null, string $delim = ' '): string
    {
        return $this->to('eb', $precision, $delim);
    }

    public function toZb(?int $precision = null, string $delim = ' '): string
    {
        return $this->to('zb', $precision, $delim);
    }

    public function toYb(?int $precision = null, string $delim = ' '): string
    {
        return $this->to('yb', $precision, $delim);
    }

    /**
     * @param Byte|int|float|string $operand
     * Returns -1 if $this->value is less than $operand.
     * Returns 1 if $this->value is greater than $operand.
     * Returns 0 if $this->value and $operand are equal
     * @return int
     */
    public function compare(self|int|float|string $operand): int
    {
        $operand = self::parse($operand);

        if ($operand == $this->value) return self::COMPARE_EQ;
        if ($this->value > $operand) return self::COMPARE_GT;

        return self::COMPARE_LT;
    }

    /**
     * @param Byte|int|float|string $value
     * @return $this
     */
    public function increment(self|int|float|string $value): self
    {
        return new self($this->value + self::parse($value));
    }

    /**
     * @param Byte|int|float|string $value
     * @return $this
     */
    public function decrement(self|int|float|string $value): self
    {
        if (($value = self::parse($value)) > $this->value) {
            throw new \LogicException('[$value] can not be greater than '. $this->value);
        }

        return new self($this->value - $value);
    }


    /**
     * @param Byte|int|float|string $operand
     * @return bool
     */
    public function equalTo(self|int|float|string $operand): bool
    {
        return self::parse($operand) == $this->value;
    }

    public function divide(self|int|float|string $value): self
    {
        if (($value = self::parse($value)) > $this->value) {
            throw new \LogicException('[$value] can not be greater than '. $this->value);
        }

        return new self($this->value - $value);
    }

    public function multiply(self|int|float|string $value): self
    {
        return new self($this->value * $value);
    }

    /**
     * @param Byte|int|float|string $operand
     * @return bool
     */
    public function lessThan(self|int|float|string $operand): bool
    {
        return self::parse($operand) > $this->value;
    }

    /**
     * @param int|float|string $operand
     * @return bool
     */
    public function greaterThan(int|float|string $operand): bool
    {
        return $this->value > self::parse($operand);
    }

    /**
     * @param int|float $bytes
     * @return string
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
     * @param string $string
     * @return int|float
     * @throws \InvalidArgumentException
     */
    public static function parse(self|string $string): int|float
    {
        if (is_numeric($string)) return $string + 0;
        if ($string instanceof self) return $string->value;

        $bytes = trim(substr($string, 0, -2));
        $units = strtolower(trim(substr($string, -2, 2)));

        if (!is_numeric($bytes)) goto end;

        foreach (self::units as $unit => $exponent) {
            if (strtolower($unit) == $units) return $bytes * pow(self::amount, $exponent);
        }

        end:
        throw new \InvalidArgumentException('Failed to parse string');
    }
}
