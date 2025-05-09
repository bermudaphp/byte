# Byte Class

*Read this in other languages: [Russian](README.ru.md)*

## Overview

`Byte` is a robust PHP class designed for working with data size units (bytes, kilobytes, megabytes, etc.). It provides a comprehensive set of methods for conversion, comparison, arithmetic operations, and human-readable formatting.

## Features

- **Unit Conversion**: Easily convert between different data size units (B, kB, MB, GB, TB, PB, EB, ZB, YB)
- **Arithmetic Operations**: Perform addition, subtraction, multiplication, division, and modulo operations
- **Comparison**: Compare byte values with support for both individual and array-based comparisons
- **Formatting**: Format byte values as human-readable strings
- **Range Operations**: Create ranges of byte values, find min/max, calculate averages
- **Transfer Time Calculation**: Estimate data transfer times based on bandwidth

## Installation

You can install the package via composer:

```bash
composer require bermuda/byte
```

## Usage

### Creating Byte Instances

There are multiple ways to create a `Byte` instance:

```php
use Bermuda\Stdlib\Byte;

// From numeric value (bytes)
$bytes = new Byte(1024);

// From string
$bytes = new Byte('1024 kB');

// Using static factory methods
$bytes = Byte::new(1024);
$bytes = Byte::kb(1024); // 1024 kilobytes
$bytes = Byte::mb(50);   // 50 megabytes
$bytes = Byte::gb(2);    // 2 gigabytes

// From human-readable string
$bytes = Byte::fromHumanReadable('2.5 GB');

// From bits
$bytes = Byte::fromBits(8192); // 1024 bytes
```

### Converting to Different Units

```php
$bytes = new Byte(1536);

// Convert to human-readable string
echo $bytes->toString(); // "1.5 kB"

// Convert to specific units
echo $bytes->toKb();  // "1.5 kB"
echo $bytes->toMb();  // "0.0015 MB"
echo $bytes->toGb();  // "0.000001 GB"

// Customize format
echo $bytes->to('kb', 3, '_'); // "1.500_kB"

// Get raw value in specific unit
$kbValue = $bytes->getValue('kb'); // 1.5
```

### Comparison Operations

The class supports both single-value and multi-value comparisons with two modes:
- `MODE_ALL`: Returns true only if the condition is true for all values
- `MODE_ANY`: Returns true if the condition is true for at least one value

```php
$bytes = Byte::kb(1024); // 1 MB

// Compare with a single value
$bytes->equalTo('1 MB');        // true
$bytes->greaterThan('900 kB');  // true
$bytes->lessThan('1.1 MB');     // true

// Compare with multiple values
$bytes->greaterThan(['900 kB', '1.1 MB'], Byte::MODE_ANY); // true
$bytes->greaterThan(['900 kB', '1.1 MB'], Byte::MODE_ALL); // false

// Other comparison methods
$bytes->lessThanOrEqual('1 MB');                    // true
$bytes->greaterThanOrEqual(['1 MB', '1024 kB']);    // true
$bytes->between('900 kB', '1.1 MB');                // true
$bytes->inRanges([['500 kB', '800 kB'], ['1 MB', '1.5 MB']]); // true
```

### Arithmetic Operations

```php
$bytes = Byte::mb(1);

// Addition
$newBytes = $bytes->increment('500 kB'); // 1.5 MB

// Subtraction
$newBytes = $bytes->decrement('512 kB'); // ~0.5 MB

// Division
$newBytes = $bytes->divide(2); // 512 KB

// Multiplication
$newBytes = $bytes->multiply(3); // 3 MB

// Modulo
$newBytes = $bytes->modulo('512 kB'); // 0

// Absolute value
$newBytes = (new Byte(-1024))->abs(); // 1024 bytes

// Min/Max
$newBytes = $bytes->max('1.5 MB'); // 1.5 MB
$newBytes = $bytes->min(['2 MB', '500 kB']); // 500 kB
```

### Static Operations on Collections

```php
// Create a range
$range = Byte::range('1 MB', '5 MB', '1 MB'); // [1 MB, 2 MB, 3 MB, 4 MB, 5 MB]

// Calculate sum
$sum = Byte::sum(['1 MB', '2 MB', '500 kB']); // 3.5 MB

// Calculate average
$avg = Byte::average(['1 MB', '2 MB', '3 MB']); // 2 MB

// Find maximum/minimum
$max = Byte::maximum(['1 MB', '500 kB', '2 GB']); // 2 GB
$min = Byte::minimum(['1 MB', '500 kB', '2 GB']); // 500 kB
```

### Bit Conversion

```php
$bytes = new Byte(1024);

// Convert to bits
$bits = $bytes->toBits(); // 8192

// Create from bits
$bytes = Byte::fromBits(8192); // 1024 bytes
```

### Transfer Time Calculation

```php
$fileSize = Byte::gb(1);
$bandwidth = Byte::mb(10); // 10 MB/s

// Calculate transfer time in seconds
$seconds = $fileSize->getTransferTime($bandwidth); // 102.4 seconds

// Get formatted transfer time
$time = $fileSize->getFormattedTransferTime($bandwidth); // "1 minute, 42 seconds"
```

### Humanizing Byte Values

```php
// Format byte values for human readability
echo Byte::humanize(1536);             // "1.5 kB"
echo Byte::humanize(1536, 3);          // "1.500 kB"
echo Byte::humanize(1536, 2, '_');     // "1.5_kB" 
```

### State Checking

```php
$bytes = new Byte(1024);

$bytes->isZero();      // false
$bytes->isPositive();  // true
$bytes->isNegative();  // false
```

## Error Handling

The class throws exceptions in the following situations:

- `\InvalidArgumentException`: When parsing invalid string formats or using unsupported units
- `\LogicException`: When attempting to decrement by a value greater than the current value
- `\DivisionByZeroError`: When attempting to divide by zero

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).
