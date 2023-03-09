# Install
```bash
composer require bermudaphp/byte
````
# Usage
```php
$byte = new Byte(100*1024*1024);

echo $byte->to('gb', 2); // 0.1 GB
echo $byte->toString(); // 100 MB
echo $byte->value; // 104857600

$operand = 101*1024*1024;

// Returns -1 if $byte->value is less than $operand. Returns 1 if $byte->value is greater than $operand. Returns 0 if $byte->value and $operand are equal
$result = $byte->compare($operand) // -1

$result = $byte->equalTo($operand) // false
$result = $byte->lessThan($operand) // true
$result = $byte->greaterThan($operand) // false

Byte::humanize($operand); // 101 MB
Byte::parse('101 mb'); // 105906176
```
