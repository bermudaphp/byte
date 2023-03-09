# Install
```bash
composer require bermudaphp/byte
````
# Usage
```php
$byte = new Byte(100*pow(1024, 2)); // or Byte::mb(100)

echo $byte->to('gb', 2); // 0.1 GB or $byte->toGb(2);
echo $byte->toString(); // 100 MB
echo $byte->value; // 104857600

$operand = 101*pow(1024, 2);

// Returns -1 if $byte->value is less than $operand. Returns 1 if $byte->value is greater than $operand. Returns 0 if $byte->value and $operand are equal
$byte->compare($operand) // -1
$byte->compare('999kb') // 1

$byte->equalTo($operand) // false
$byte->lessThan($operand) // true
$byte->greaterThan($operand) // false

($byte = $byte->increment('100mb'))->value; // 209715200
($byte = $byte->decrement('50mb'))->value; // 157286400

Byte::humanize($operand); // 101 MB
Byte::parse('101 mb'); // 105906176
```
