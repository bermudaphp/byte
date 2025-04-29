# Byte

Byte is a PHP library for working with byte values. It allows you to create objects that represent byte quantities, perform arithmetic operations and comparisons on them, and convert them into human-readable strings with appropriate units (B, kB, MB, GB, TB, PB, EB, ZB, YB).

## Features

- **Unit Conversion**: Convert byte values between various units (bytes, kilobytes, megabytes, etc.).
- **Arithmetic Operations**: Increment, decrement, multiply, and divide byte values.
- **Comparison**: Compare byte amounts (greater than, less than, equal to).
- **Human-Readable Output**: Automatically convert byte values to a string representation in the most appropriate unit.
- **Strict Typing**: Uses modern PHP features (union types, readonly properties, etc.) to ensure code safety.

## Installation

You can install the library via [Composer](https://getcomposer.org/):

```bash
composer require bermudaphp/byte
```
## Usage

```php
use Bermuda\Stdlib\Byte;

// Create a Byte object from a string with a unit
$byte = new Byte('1024 kB');

// Output the value in megabytes rounded to 2 decimal places
echo $byte->toMb(2); // For example, might output "1 MB"

// Arithmetic operations:
$byteIncremented = $byte->increment(1024); // Increase the value by 1024 bytes
echo $byteIncremented->toString();

// Comparison:
if ($byte->greaterThan(500)) {
    echo "The value is greater than 500 bytes";
}
```

## API

# Constructor

```php
public function __construct(int|float|string $value)
```
Creates a new Byte instance. The constructor accepts a number or a string representing the value and (optionally) its unit (e.g., "1024 kB").

# String Conversion Methods

to(string $units = 'b', ?int $precision = null, string $delim = ' '): string Converts the internal byte value into a formatted string in the specified unit. Parameters:

$units: The target unit for conversion (e.g., "b", "kb", "mb", etc.). The case is ignored.

$precision: (Optional) The number of decimal places for rounding. If null, no rounding is applied.

$delim: The delimiter to use between the numeric value and the unit.

toKb(?int $precision = null, string $delim = ' '): string A convenience method that returns the value formatted in kilobytes.

toMb(?int $precision = null, string $delim = ' '): string Converts the value to megabytes.

toGb(?int $precision = null, string $delim = ' '): string Converts the value to gigabytes.

toTb(?int $precision = null, string $delim = ' '): string Converts the value to terabytes.

toPb(?int $precision = null, string $delim = ' '): string Converts the value to petabytes.

toEb(?int $precision = null, string $delim = ' '): string Converts the value to exabytes.

toZb(?int $precision = null, string $delim = ' '): string Converts the value to zettabytes.

toYb(?int $precision = null, string $delim = ' '): string Converts the value to yottabytes.

# Arithmetic Operations

increment($value): Byte Returns a new Byte instance with its value incremented by the specified amount.

decrement($value): Byte Returns a new Byte instance with its value decremented by the specified amount (the decrement value must not exceed the current value).

multiply($value): Byte Returns a new Byte instance equal to the current value multiplied by the specified amount.

divide($value): Byte Returns a new Byte instance equal to the current value divided by the specified amount (ensuring that the divisor does not exceed the current value).

# Comparison

compare($operand): int Compares the current byte value with another value and returns one of the following:

Byte::COMPARE_LT if the current value is less.

Byte::COMPARE_EQ if both values are equal.

Byte::COMPARE_GT if the current value is greater.

equalTo($operand): bool Checks if the current value is equal to the given operand.

lessThan($operand): bool Evaluates whether the current value is less than the specified operand.

greaterThan($operand): bool Evaluates whether the current value is greater than the specified operand.

# String Representation

__toString() and toString() Return a human-readable string representation of the Byte instance, using the most appropriate unit.

# Parsing Values

parse($value): int|float Converts a given string or numeric value into its numeric byte representation. The method expects the string to contain a numeric portion followed by a unit (e.g., "1024 kB"). Error: If the numeric portion is invalid or the unit is not recognized, the method throws an \InvalidArgumentException with one of the following messages:

"Failed to parse string: The numeric portion is invalid." – if the numeric part is not valid.

"Failed to parse string: Unrecognized unit." – if the unit is not supported.
