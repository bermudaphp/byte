<?php

namespace Bermuda\Stdlib;

final class Byte implements \Stringable
{
    public readonly int $value;
    
    public const COMPARE_LT = -1;
    public const COMPARE_EQ = 0;
    public const COMPARE_GT = 1;
    
    public function __construct(int|string $value)
    {
        $this->value = self::parse($value);
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

    public function to(string $units = 'b', int $precision = null): string
    {
        if ($precision) {
            return match (strtolower($units)) {
                'kb' => round($this->value / 1024, $precision) . ' kB',
                'mb' => round($this->value / pow(1024, 2), $precision) . ' MB',
                'gb' => round($this->value / pow(1024, 3), $precision) . ' GB',
                'tb' => round($this->value / pow(1024, 4), $precision) . ' TB',
                default => $this->value . ' B'
            };
        }

        return match (strtolower($units)) {
            'kb' => $this->value / 1024 . ' kB',
            'mb' => $this->value / pow(1024, 2) . ' MB',
            'gb' => $this->value / pow(1024, 3) . ' GB',
            'tb' => $this->value / pow(1024, 4) . ' TB',
            default => $this->value . ' B'
        };
    }

    public function toKb(int $precision = null): string
    {
        return $this->to('kB', $precision);
    }

    public function toMb(int $precision = null): string
    {
        return $this->to('mb', $precision);
    }

    public function toGb(int $precision = null): string
    {
        return $this->to('gb', $precision);
    }

    public function toTb(int $precision = null): string
    {
        return $this->to('tb', $precision);
    }

    /**
     * @return int
     */
    public function toInt(): int
    {
        return $this->value;
    }

    /**
     * @param Byte|int|string $operand
     * Returns -1 if $this->value is less than $operand.
     * Returns 1 if $this->value is greater than $operand.
     * Returns 0 if $this->value and $operand are equal
     * @return int
     */
    public function compare(self|int|string $operand): int
    {
        $operand = self::parse($operand);

        if ($operand == $this->value) return self::COMPARE_EQ;
        if ($this->value > $operand) return self::COMPARE_GT;

        return self::COMPARE_LT;
    }

    /**
     * @param Byte|int|string $value
     * @return $this
     */
    public function increment(self|int|string $value): self
    {
        return new self($this->value + self::parse($value));
    }

    /**
     * @param Byte|int|string $value
     * @return $this
     */
    public function decrement(self|int|string $value): self
    {
        if (($value = self::parse($value)) > $this->value) {
            throw new \LogicException('[$value] can not be greater than '. $this->value);
        }

        return new self($this->value - $value);
    }


    /**
     * @param Byte|int|string $operand
     * @return bool
     */
    public function equalTo(self|int|string $operand): bool
    {
        return self::parse($operand) == $this->value;
    }

    /**
     * @param Byte|int|string $operand
     * @return bool
     */
    public function lessThan(self|int|string $operand): bool
    {
        return self::parse($operand) > $this->value;
    }

    /**
     * @param int|string $operand
     * @return bool
     */
    public function greaterThan(int|string $operand): bool
    {
        return $this->value > self::parse($operand);
    }

    /**
     * @param int $bytes
     * @return string
     */
    public static function humanize(int $bytes): string
    {
        return round($bytes / pow(1024, $i = floor(log($bytes, 1024))), [0,0,2,2,3][$i]).[' B',' kB',' MB',' GB',' TB'][$i];
    }

    /**
     * @param string $string
     * @return int
     * @throws \InvalidArgumentException
     */
    public static function parse(self|string $string): int
    {
        if (is_numeric($string)) return $string;
        if ($string instanceof self) return $string->value;

        if (
            str_ends_with($string = strtolower($string), 'b')
            && is_numeric($bytes = substr($string, 0, -1))
        ) {
            return $bytes;
        }

        $isNumeric = is_numeric($bytes = trim(substr($string, 0, -2)));

        if (($n = substr($string, -2, 2)) == 'kb' && $isNumeric) return $bytes * 1024;
        if ($n == 'mb' && $isNumeric) return $bytes * pow(1024, 2);
        if ($n == 'gb' && $isNumeric) return $bytes * pow(1024, 3);
        if ($n == 'tb' && $isNumeric) return $bytes * pow(1024, 4);

        throw new \InvalidArgumentException('Failed to parse string');
    }
}
