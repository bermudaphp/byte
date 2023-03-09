<?php

namespace Bermuda\Stdlib;

final class Byte implements \Stringable
{
    public readonly int|float $value;
    
    public const COMPARE_LT = -1;
    public const COMPARE_EQ = 0;
    public const COMPARE_GT = 1;
    
    public function __construct(int|float|string $value)
    {
        $this->value = self::parse($value);
    }

    /**
     * @param int $value
     * @return static
     */
    public static function kb(int $value): self
    {
        return new self($value*pow(1000, 1));
    }

    /**
     * @param int $value
     * @return static
     */
    public static function mb(int $value): self
    {
        return new self($value*pow(1000, 2));
    }

    /**
     * @param int $value
     * @return static
     */
    public static function gb(int $value): self
    {
        return new self($value*pow(1000, 3));
    }

    /**
     * @param int $value
     * @return static
     */
    public static function tb(int $value): self
    {
        return new self($value*pow(1000, 4));
    }

    /**
     * @param int $value
     * @return static
     */
    public static function pb(int $value): self
    {
        return new self($value*pow(1000, 5));
    }

    /**
     * @param int $value
     * @return static
     */
    public static function eb(int $value): self
    {
        return new self($value*pow(1000, 6));
    }

    /**
     * @param int $value
     * @return static
     */
    public static function zb(int $value): self
    {
        return new self($value*pow(1000, 7));
    }

    /**
     * @param int $value
     * @return static
     */
    public static function yb(int $value): self
    {
        return new self($value*pow(1000, 8));
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

    public function to(string $units = 'b', int $precision = null, string $delim = ' '): string
    {
        $segments = match (strtolower($units)) {
            'kb' => [$this->value / 1000, 'kB'],
            'mb' => [$this->value / pow(1000, 2),  'MB'],
            'gb' => [$this->value / pow(1000, 3), 'GB'],
            'tb' => [$this->value / pow(1000, 4), 'TB'],
            'pb' => [$this->value / pow(1000, 5), 'PB'],
            'eb' => [$this->value / pow(1000, 6), 'EB'],
            'zb' => [$this->value / pow(1000, 7), 'ZB'],
            'yb' => [$this->value / pow(1000, 8), 'YB'],
            default => [$this->value, 'B']
        };

        if ($precision) {
            return round($segments[0], $precision) . "{$delim}{$segments[1]}";
        }

        return implode($delim, $segments);
    }

    public function toKb(int $precision = null, string $delim = ' '): string
    {
        return $this->to('kB', $precision, $delim);
    }

    public function toMb(int $precision = null, string $delim = ' '): string
    {
        return $this->to('mb', $precision, $delim);
    }

    public function toGb(int $precision = null, string $delim = ' '): string
    {
        return $this->to('gb', $precision, $delim);
    }

    public function toTb(int $precision = null, string $delim = ' '): string
    {
        return $this->to('tb', $precision, $delim);
    }

    public function toPb(int $precision = null, string $delim = ' '): string
    {
        return $this->to('pb', $precision, $delim);
    }

    public function toEb(int $precision = null, string $delim = ' '): string
    {
        return $this->to('eb', $precision, $delim);
    }

    public function toZb(int $precision = null, string $delim = ' '): string
    {
        return $this->to('zb', $precision, $delim);
    }

    public function toYb(int $precision = null, string $delim = ' '): string
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
        $exponent = 8;
        $units = ['kB','MB','GB','TB', 'PB', 'EB', 'ZB', 'YB'];
        while ($exponent > 0) {
            if (($result = $bytes / pow(1000, $exponent--)) >= 1) {
                if ($precision) $result = round($result, $precision);
                return "$result$delim$units[$exponent]";
            }
        }

        return "$bytes{$delim}B";
    }

    /**
     * @param string $string
     * @return int
     * @throws \InvalidArgumentException
     */
    public static function parse(self|string $string): int|float
    {
        if (is_numeric($string)) return $string;
        if ($string instanceof self) return $string->value;

        if (
            str_ends_with($string = strtolower($string), 'b')
            && is_numeric($bytes = trim(substr($string, 0, -1)))
        ) {
            return $bytes;
        }

        $isNumeric = is_numeric($bytes = trim(substr($string, 0, -2)));

        if (($n = substr($string, -2, 2)) == 'kb' && $isNumeric) return $bytes * 1000;
        if ($n == 'mb' && $isNumeric) return $bytes * pow(1000, 2);
        if ($n == 'gb' && $isNumeric) return $bytes * pow(1000, 3);
        if ($n == 'tb' && $isNumeric) return $bytes * pow(1000, 4);
        if ($n == 'pb' && $isNumeric) return $bytes * pow(1000, 5);
        if ($n == 'eb' && $isNumeric) return $bytes * pow(1000, 6);
        if ($n == 'zb' && $isNumeric) return $bytes * pow(1000, 7);
        if ($n == 'yb' && $isNumeric) return $bytes * pow(1000, 8);

        throw new \InvalidArgumentException('Failed to parse string');
    }
}
