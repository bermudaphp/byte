<?php

namespace Bermuda\Utils;

final class Byte implements \Stringable
{
    public readonly int $value;
    
    public const COMPARE_LT = -1;
    public const COMPARE_EQ = 0;
    public const COMPARE_GT = 1;
    
    public function __construct(int|string $value)
    {
        if (is_string($value)) {
            $value = self::parse($value);
        }

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return self::humanize($this->value);
    }

    /**
     * @return int
     */
    public function toInt(): int
    {
        return $this->value;
    }

    /**
     * @param int|string $size
     * @return int
     */
    public function compare(int|string $size): int
    {
        !is_string($size) ?: $size = self::parse($size);

        if ($size == $this->value) {
            return self::COMPARE_EQ;
        }

        if ($size > $this->value) {
            return self::COMPARE_GT;
        }

        return self::COMPARE_LT;
    }
    
    public function increment(int|string $size): self
    {
        $this->value += self::parse($size);
        return $this;
    }
    
    public function decrement(int|string $size): self
    {
        if (($size = self::parse($size)) > $this->value) {
            throw new \LogicException('[ $size ] can not be greater than '. $this->value);
        }
        
        $this->value -= $size;
        return $this;
    }


    /**
     * @param int|string $size
     * @return bool
     */
    public function equalTo(int|string $size): bool
    {
        return (is_string($size) ? self::parse($size) : $size) == $this->value;
    }

    /**
     * @param int|string $size
     * @return bool
     */
    public function lessThan(int|string $size): bool
    {
        return $this->value > (is_string($size) ? self::parse($size) : $size);
    }

    /**
     * @param int|string $size
     * @return bool
     */
    public function greaterThan(int|string $size): bool
    {
        return $this->value < (is_string($size) ? self::parse($size) : $size);
    }

    /**
     * @param int $bytes
     * @return string
     */
    public static function humanize(int $bytes): string
    {
        return round($bytes / pow(1024, $i = floor(log($bytes, 1024))), [0,0,2,2,3][$i]).['B','kB','MB','GB','TB'][$i];
    }

    /**
     * @param string $humanized
     * @return int
     * @throws \InvalidArgumentException
     */
    public static function parse(string $humanized): int
    {
        if (is_numeric($humanized)) {
            return $humanized;
        }
        
        if (str_ends_with($humanized, 'B') && is_numeric($bytes = substr($humanized, 0, -1))) {
            return $bytes;
        }

        $isNumeric = is_numeric($bytes = substr($humanized, 0, -2));

        if (($n = substr($humanized, -2, 2)) == 'kB' && $isNumeric) {
            return $bytes * 1024;
        }

        if ($n == 'MB' && $isNumeric) {
            return $bytes * pow(1024, 2);
        }

        if ($n == 'GB' && $isNumeric) {
            return $bytes * pow(1024, 3);
        }

        if ($n == 'TB' && $isNumeric) {
            return $bytes * pow(1024, 4);
        }

        throw new \InvalidArgumentException('Failed to parse string');
    }
}
