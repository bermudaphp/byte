# Классы Byte и BitRate
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.4-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](https://opensource.org/licenses/MIT)

*Read this in other languages: [English](README.md)*

## Обзор

Библиотека предоставляет PHP-классы для работы с размерами данных (Byte) и скоростью передачи данных (BitRate) с полным набором функций для конвертации, сравнения, арифметических операций и форматирования в удобочитаемом виде.

## Возможности

- **Конвертация единиц измерения**: Легко преобразуйте данные между различными единицами (B, kB, MB, GB, TB, PB, EB, ZB, YB)
- **Обработка скорости передачи**: Работайте со скоростями в различных единицах (bps, kbps, Mbps, Gbps и т.д.)
- **Арифметические операции**: Выполняйте сложение, вычитание, умножение, деление и операции по модулю
- **Сравнение**: Сравнивайте значения с поддержкой как индивидуальных, так и массовых сравнений
- **Форматирование**: Форматируйте значения в удобочитаемые строки с поддержкой нескольких языков
- **Операции с диапазонами**: Создавайте диапазоны значений, находите мин/макс, вычисляйте средние значения
- **Расчет времени передачи**: Оценивайте время передачи данных на основе пропускной способности
- **Интернационализация**: Форматируйте временные интервалы на разных языках

## Установка

Вы можете установить пакет через composer:

```bash
composer require bermudaphp/byte
```

## Использование

### Класс Byte

#### Создание экземпляров Byte

Существует несколько способов создания экземпляра `Byte`:

```php
use Bermuda\Stdlib\Byte;

// Из числового значения (байты)
$bytes = new Byte(1024);

// Из строки
$bytes = new Byte('1024 kB');

// Использование статических фабричных методов
$bytes = Byte::new(1024);
$bytes = Byte::kb(1024); // 1024 килобайта
$bytes = Byte::mb(50);   // 50 мегабайт
$bytes = Byte::gb(2);    // 2 гигабайта

// Из человекочитаемой строки
$bytes = Byte::fromHumanReadable('2.5 GB');

// Из битов
$bytes = Byte::fromBits(8192); // 1024 байта
```

#### Конвертация в различные единицы измерения

```php
$bytes = new Byte(1536);

// Конвертация в человекочитаемую строку
echo $bytes->toString(); // "1.5 kB"

// Конвертация в определенные единицы
echo $bytes->toKb();  // "1.5 kB"
echo $bytes->toMb();  // "0.0015 MB"
echo $bytes->toGb();  // "0.000001 GB"

// Настройка формата
echo $bytes->to('kb', 3, '_'); // "1.500_kB"

// Получение чистого значения в конкретной единице
$kbValue = $bytes->getValue('kb'); // 1.5
```

#### Операции сравнения

Класс поддерживает как сравнение с одиночными значениями, так и с множественными значениями в двух режимах:
- `MODE_ALL`: Возвращает true, только если условие верно для всех значений
- `MODE_ANY`: Возвращает true, если условие верно хотя бы для одного значения

```php
$bytes = Byte::kb(1024); // 1 MB

// Сравнение с одним значением
$bytes->equalTo('1 MB');        // true
$bytes->greaterThan('900 kB');  // true
$bytes->lessThan('1.1 MB');     // true

// Сравнение с несколькими значениями
$bytes->greaterThan(['900 kB', '1.1 MB'], Byte::MODE_ANY); // true
$bytes->greaterThan(['900 kB', '1.1 MB'], Byte::MODE_ALL); // false

// Другие методы сравнения
$bytes->lessThanOrEqual('1 MB');                    // true
$bytes->greaterThanOrEqual(['1 MB', '1024 kB']);    // true
$bytes->between('900 kB', '1.1 MB');                // true
$bytes->inRanges([['500 kB', '800 kB'], ['1 MB', '1.5 MB']]); // true
```

#### Арифметические операции

```php
$bytes = Byte::mb(1);

// Сложение
$newBytes = $bytes->increment('500 kB'); // 1.5 MB

// Вычитание
$newBytes = $bytes->decrement('512 kB'); // ~0.5 MB

// Деление
$newBytes = $bytes->divide(2); // 512 KB

// Умножение
$newBytes = $bytes->multiply(3); // 3 MB

// Остаток от деления
$newBytes = $bytes->modulo('512 kB'); // 0

// Абсолютное значение
$newBytes = (new Byte(-1024))->abs(); // 1024 байта

// Минимум/Максимум
$newBytes = $bytes->max('1.5 MB'); // 1.5 MB
$newBytes = $bytes->min(['2 MB', '500 kB']); // 500 kB
```

#### Статические операции над коллекциями

```php
// Создание диапазона
$range = Byte::range('1 MB', '5 MB', '1 MB'); // [1 MB, 2 MB, 3 MB, 4 MB, 5 MB]

// Вычисление суммы
$sum = Byte::sum(['1 MB', '2 MB', '500 kB']); // 3.5 MB

// Вычисление среднего
$avg = Byte::average(['1 MB', '2 MB', '3 MB']); // 2 MB

// Поиск максимума/минимума
$max = Byte::maximum(['1 MB', '500 kB', '2 GB']); // 2 GB
$min = Byte::minimum(['1 MB', '500 kB', '2 GB']); // 500 kB
```

### Класс BitRate

#### Создание экземпляров BitRate

```php
use Bermuda\Stdlib\BitRate;

// Из числового значения (биты в секунду)
$rate = new BitRate(1_000_000); // 1 Mbps

// Использование статических фабричных методов для битовых скоростей
$rate = BitRate::bps(1000);     // 1000 бит в секунду
$rate = BitRate::kbps(1000);    // 1000 килобит в секунду
$rate = BitRate::mbps(10);      // 10 мегабит в секунду
$rate = BitRate::gbps(1);       // 1 гигабит в секунду

// Использование статических фабричных методов для байтовых скоростей
$rate = BitRate::bytesPerSec(125_000);  // 125 КБ/с (эквивалент 1 Mbps)
$rate = BitRate::kBps(1);               // 1 килобайт в секунду (8 kbps)
$rate = BitRate::mBps(1);               // 1 мегабайт в секунду (8 Mbps)
$rate = BitRate::gBps(1);               // 1 гигабайт в секунду (8 Gbps)

// Из любой строки с единицей измерения
$rate = BitRate::from(10, 'Mbps');      // 10 Mbps
$rate = BitRate::from(1.5, 'GBps');     // 1.5 GB/s

// Из человекочитаемой строки
$rate = BitRate::fromHumanReadable('10 Mbps');
```

#### Конвертация между единицами измерения

```php
$rate = BitRate::mbps(100);  // 100 Mbps

// Получение значения в битах или байтах
$bitsPerSec = $rate->toBits();    // 100,000,000 bps
$bytesPerSec = $rate->toBytes();  // 12,500,000 B/s

// Конвертация в человекочитаемые форматы
echo $rate->toString();           // "100 Mbps"
echo $rate->toString('byte');     // "12.5 MBps"

// Конвертация в определенные единицы
echo $rate->toMbps();             // "100 Mbps"
echo $rate->toGbps();             // "0.1 Gbps"
echo $rate->toMBps();             // "12.5 MBps"
echo $rate->toKBps();             // "12500 kBps"

// Настройка формата вывода
echo $rate->to('Mbps', 3, '_');   // "100.000_Mbps"
```

#### Операции сравнения

```php
$rate = BitRate::mbps(100);  // 100 Mbps

// Сравнение с другой скоростью
$rate->equalTo(BitRate::kbps(100000));           // true
$rate->equalTo('100 Mbps');                      // true
$rate->equalTo('12.5 MBps');                     // true (эквивалент в байтах)

// Сравнения больше/меньше
$rate->greaterThan(BitRate::mbps(50));           // true
$rate->lessThan(BitRate::gbps(1));               // true

// Сравнение с массивами
$rate->greaterThan(['10 Mbps', '150 Mbps'], BitRate::MODE_ANY);  // true
```

#### Арифметические операции

```php
$rate = BitRate::mbps(100);

// Сложение
$newRate = $rate->increment(BitRate::mbps(50));    // 150 Mbps

// Вычитание
$newRate = $rate->decrement(BitRate::mbps(30));    // 70 Mbps

// Умножение
$newRate = $rate->multiply(2);                     // 200 Mbps

// Деление
$newRate = $rate->divide(4);                       // 25 Mbps

// Ограничение скорости (частный случай умножения)
$throttledRate = $rate->throttle(0.8);             // 80 Mbps (80% от исходной)
```

#### Расчеты передачи данных

```php
$rate = BitRate::mbps(100);      // Скорость скачивания 100 Mbps
$fileSize = Byte::gb(1);         // Файл размером 1 GB

// Расчет времени передачи
$seconds = $rate->calculateTransferTime($fileSize);  // 80 секунд

// Получение отформатированного времени передачи
$time = $rate->getFormattedTransferTime($fileSize);  // "1 минута, 20 секунд"

// Расчет объема данных для заданного времени
$downloadedSize = $rate->calculateTransferAmount(60);  // 750 MB за 60 секунд

// Оценка размера файла для потоковой передачи
$streamingRate = BitRate::mbps(5);                    // Видеопоток 5 Mbps
$videoDuration = 3600;                                // 1 час в секундах
$videoSize = $streamingRate->estimateFileSize($videoDuration);  // ~2.25 GB
```

#### Статические операции над коллекциями

```php
$rates = [
    BitRate::mbps(10),
    BitRate::mbps(50),
    BitRate::mbps(100)
];

// Вычисление среднего
$avgRate = BitRate::average($rates);  // 53.33 Mbps

// Поиск максимума/минимума
$maxRate = BitRate::maximum($rates);  // 100 Mbps
$minRate = BitRate::minimum($rates);  // 10 Mbps

// Вычисление суммы
$totalRate = BitRate::sum($rates);    // 160 Mbps

// Создание диапазона
$rangeRates = BitRate::range(
    BitRate::mbps(10), 
    BitRate::mbps(50), 
    BitRate::mbps(10)
);  // [10 Mbps, 20 Mbps, 30 Mbps, 40 Mbps, 50 Mbps]
```

### BitFormatter для интернационализации

Класс `BitFormatter` предоставляет функциональность форматирования с поддержкой нескольких языков:

```php
use Bermuda\Stdlib\BitFormatter;
use Bermuda\Stdlib\Byte;
use Bermuda\Stdlib\BitRate;

// Загрузка файлов переводов
BitFormatter::loadLanguage('/path/to/translations/en.php');
BitFormatter::loadLanguage('/path/to/translations/fr.php');
BitFormatter::loadLanguage('/path/to/translations/ru.php');

// Или загрузка всех переводов из директории
BitFormatter::loadLanguagesFromDirectory('/path/to/translations');

/* Библиотека поставляется со встроенными переводами для нескольких языков. Вы можете загрузить все доступные переводы сразу с помощью метода `loadDefaults()`*/
BitFormatter::loadDefaults();

// Форматирование времени на разных языках
$fileSize = Byte::gb(2);
$downloadSpeed = BitRate::mbps(25);
$seconds = BitFormatter::calculateTransferTime($fileSize, $downloadSpeed);

echo BitFormatter::formatTime($seconds, 'en');  // "10 minutes, 40 seconds"
echo BitFormatter::formatTime($seconds, 'fr');  // "10 minutes et 40 secondes"
echo BitFormatter::formatTime($seconds, 'ru');  // "10 минут и 40 секунд"

// Установка языка по умолчанию
BitFormatter::setDefaultLanguage('ru');
echo BitFormatter::formatTime($seconds);        // "10 минут и 40 секунд"

// Прямое форматирование с классами BitRate и Byte
echo $downloadSpeed->getFormattedTransferTime($fileSize, 'en');  // "10 minutes, 40 seconds"
echo $fileSize->getFormattedTransferTime($downloadSpeed, 'ru');  // "10 минут и 40 секунд"

// Форматирование значений данных
echo BitFormatter::humanizeBytes(1536);  // "1.5 kB"
echo BitFormatter::humanizeBitRate(1_000_000, 'bit');  // "1 Mbps"
echo BitFormatter::humanizeBitRate(1_000_000, 'byte');  // "125 kBps"
```

## Обработка ошибок

Классы выбрасывают исключения в следующих ситуациях:

- `\InvalidArgumentException`: При парсинге недопустимых форматов строк или использовании неподдерживаемых единиц измерения
- `\LogicException`: При попытке уменьшить значение на величину, большую текущего значения
- `\DivisionByZeroError`: При попытке деления на ноль

## Лицензия

Этот пакет является программным обеспечением с открытым исходным кодом, лицензированным под [лицензией MIT](LICENSE).
