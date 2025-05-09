# Byte and BitRate Classes
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.4-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](https://opensource.org/licenses/MIT)

*Read this in other languages: [Russian](README.ru.md)*

## Overview

The library provides PHP classes for working with data sizes (Byte) and data transfer rates (BitRate) with comprehensive functionality for conversion, comparison, arithmetic operations, and human-readable formatting.

## Features

- **Unit Conversion**: Easily convert between different data units (B, kB, MB, GB, TB, PB, EB, ZB, YB)
- **Transfer Rate Handling**: Work with bit rates in various units (bps, kbps, Mbps, Gbps, etc.)
- **Arithmetic Operations**: Perform addition, subtraction, multiplication, division, and modulo operations
- **Comparison**: Compare values with support for both individual and array-based comparisons
- **Formatting**: Format values as human-readable strings with multi-language support
- **Range Operations**: Create ranges of values, find min/max, calculate averages
- **Transfer Time Calculation**: Estimate data transfer times based on bandwidth
- **Internationalization**: Format time durations in multiple languages

## Installation

You can install the package via composer:

```bash
composer require bermudaphp/byte
```

## Usage

### Byte Class

#### Creating Byte Instances

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

#### Converting to Different Units

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

#### Comparison Operations

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

#### Arithmetic Operations

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

#### Static Operations on Collections

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

### BitRate Class

#### Creating BitRate Instances

```php
use Bermuda\Stdlib\BitRate;

// From numeric value (bits per second)
$rate = new BitRate(1_000_000); // 1 Mbps

// Using static factory methods for bit-based rates
$rate = BitRate::bps(1000);     // 1000 bits per second
$rate = BitRate::kbps(1000);    // 1000 kilobits per second
$rate = BitRate::mbps(10);      // 10 megabits per second
$rate = BitRate::gbps(1);       // 1 gigabit per second

// Using static factory methods for byte-based rates
$rate = BitRate::bytesPerSec(125_000);  // 125 KB/s (equivalent to 1 Mbps)
$rate = BitRate::kBps(1);               // 1 kilobyte per second (8 kbps)
$rate = BitRate::mBps(1);               // 1 megabyte per second (8 Mbps)
$rate = BitRate::gBps(1);               // 1 gigabyte per second (8 Gbps)

// From any unit string
$rate = BitRate::from(10, 'Mbps');      // 10 Mbps
$rate = BitRate::from(1.5, 'GBps');     // 1.5 GB/s

// From human-readable string
$rate = BitRate::fromHumanReadable('10 Mbps');
```

#### Converting Between Units

```php
$rate = BitRate::mbps(100);  // 100 Mbps

// Get value in bits or bytes
$bitsPerSec = $rate->toBits();    // 100,000,000 bps
$bytesPerSec = $rate->toBytes();  // 12,500,000 B/s

// Convert to human-readable formats
echo $rate->toString();           // "100 Mbps"
echo $rate->toString('byte');     // "12.5 MBps"

// Convert to specific units
echo $rate->toMbps();             // "100 Mbps"
echo $rate->toGbps();             // "0.1 Gbps"
echo $rate->toMBps();             // "12.5 MBps"
echo $rate->toKBps();             // "12500 kBps"

// Customize output format
echo $rate->to('Mbps', 3, '_');   // "100.000_Mbps"
```

#### Comparison Operations

```php
$rate = BitRate::mbps(100);  // 100 Mbps

// Compare with another bit rate
$rate->equalTo(BitRate::kbps(100000));           // true
$rate->equalTo('100 Mbps');                      // true
$rate->equalTo('12.5 MBps');                     // true (bytes equivalent)

// Greater/less than comparisons
$rate->greaterThan(BitRate::mbps(50));           // true
$rate->lessThan(BitRate::gbps(1));               // true

// Compare with arrays
$rate->greaterThan(['10 Mbps', '150 Mbps'], BitRate::MODE_ANY);  // true
```

#### Arithmetic Operations

```php
$rate = BitRate::mbps(100);

// Addition
$newRate = $rate->increment(BitRate::mbps(50));    // 150 Mbps

// Subtraction
$newRate = $rate->decrement(BitRate::mbps(30));    // 70 Mbps

// Multiplication
$newRate = $rate->multiply(2);                     // 200 Mbps

// Division
$newRate = $rate->divide(4);                       // 25 Mbps

// Throttling (special case of multiplication)
$throttledRate = $rate->throttle(0.8);             // 80 Mbps (80% of original)
```

#### Transfer Calculations

```php
$rate = BitRate::mbps(100);      // 100 Mbps download speed
$fileSize = Byte::gb(1);         // 1 GB file

// Calculate transfer time
$seconds = $rate->calculateTransferTime($fileSize);  // 80 seconds

// Get formatted transfer time
$time = $rate->getFormattedTransferTime($fileSize);  // "1 minute, 20 seconds"

// Calculate transfer amount for a given time
$downloadedSize = $rate->calculateTransferAmount(60);  // 750 MB in 60 seconds

// Estimate file size for streaming
$streamingRate = BitRate::mbps(5);                    // 5 Mbps video stream
$videoDuration = 3600;                                // 1 hour in seconds
$videoSize = $streamingRate->estimateFileSize($videoDuration);  // ~2.25 GB
```

#### Static Operations on Collections

```php
$rates = [
    BitRate::mbps(10),
    BitRate::mbps(50),
    BitRate::mbps(100)
];

// Calculate average
$avgRate = BitRate::average($rates);  // 53.33 Mbps

// Find maximum/minimum
$maxRate = BitRate::maximum($rates);  // 100 Mbps
$minRate = BitRate::minimum($rates);  // 10 Mbps

// Calculate sum
$totalRate = BitRate::sum($rates);    // 160 Mbps

// Create a range
$rangeRates = BitRate::range(
    BitRate::mbps(10), 
    BitRate::mbps(50), 
    BitRate::mbps(10)
);  // [10 Mbps, 20 Mbps, 30 Mbps, 40 Mbps, 50 Mbps]
```

### BitFormatter for Internationalization

The `BitFormatter` class provides formatting functionality with multi-language support:

```php
use Bermuda\Stdlib\BitFormatter;
use Bermuda\Stdlib\Byte;
use Bermuda\Stdlib\BitRate;

// Load translation files
BitFormatter::loadLanguage('/path/to/translations/en.php');
BitFormatter::loadLanguage('/path/to/translations/fr.php');
BitFormatter::loadLanguage('/path/to/translations/ru.php');

// Or load all translations from a directory
BitFormatter::loadLanguagesFromDirectory('/path/to/translations');

// Format time in different languages
$fileSize = Byte::gb(2);
$downloadSpeed = BitRate::mbps(25);
$seconds = BitFormatter::calculateTransferTime($fileSize, $downloadSpeed);

echo BitFormatter::formatTime($seconds, 'en');  // "10 minutes, 40 seconds"
echo BitFormatter::formatTime($seconds, 'fr');  // "10 minutes et 40 secondes"
echo BitFormatter::formatTime($seconds, 'ru');  // "10 минут и 40 секунд"

// Set default language
BitFormatter::setDefaultLanguage('fr');
echo BitFormatter::formatTime($seconds);        // "10 minutes et 40 secondes"

// Direct formatting with BitRate and Byte classes
echo $downloadSpeed->getFormattedTransferTime($fileSize, 'en');  // "10 minutes, 40 seconds"
echo $fileSize->getFormattedTransferTime($downloadSpeed, 'ru');  // "10 минут и 40 секунд"

// Format data values
echo BitFormatter::humanizeBytes(1536);  // "1.5 kB"
echo BitFormatter::humanizeBitRate(1_000_000, 'bit');  // "1 Mbps"
echo BitFormatter::humanizeBitRate(1_000_000, 'byte');  // "125 kBps"
```

## Error Handling

The classes throw exceptions in the following situations:

- `\InvalidArgumentException`: When parsing invalid string formats or using unsupported units
- `\LogicException`: When attempting to decrement by a value greater than the current value
- `\DivisionByZeroError`: When attempting to divide by zero

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
