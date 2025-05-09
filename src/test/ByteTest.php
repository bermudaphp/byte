<?php

namespace Bermuda\Stdlib\Tests;

use Bermuda\Stdlib\Byte;
use PHPUnit\Framework\TestCase;

/**
 * Class ByteTest
 * Tests for the Byte class functionality
 */
class ByteTest extends TestCase
{
    /**
     * Test constructor and basic getters
     */
    public function testConstructor(): void
    {
        $byte = new Byte(1024);
        $this->assertEquals(1024, $byte->value);
        
        $byte = new Byte('1024');
        $this->assertEquals(1024, $byte->value);
        
        $byte = new Byte('1 kB');
        $this->assertEquals(1024, $byte->value);
        
        $this->expectException(\InvalidArgumentException::class);
        new Byte('invalid');
    }
    
    /**
     * Test static factory methods
     */
    public function testStaticFactoryMethods(): void
    {
        $this->assertEquals(1, Byte::b(1)->value);
        $this->assertEquals(1024, Byte::kb(1)->value);
        $this->assertEquals(1048576, Byte::mb(1)->value);
        $this->assertEquals(1073741824, Byte::gb(1)->value);
        $this->assertEquals(1099511627776, Byte::tb(1)->value);
        $this->assertEquals(1125899906842624, Byte::pb(1)->value);
        $this->assertEquals(1152921504606846976, Byte::eb(1)->value);
        
        // Test the new static method
        $byte = Byte::fromHumanReadable('2.5 GB');
        $this->assertEquals(2.5 * pow(1024, 3), $byte->value);
        
        // Test from bits
        $byte = Byte::fromBits(8);
        $this->assertEquals(1, $byte->value);
    }
    
    /**
     * Test toString and humanize methods
     */
    public function testToStringAndHumanize(): void
    {
        $byte = new Byte(1536);
        
        // Test __toString and toString aliases
        $this->assertEquals('1.5 kB', $byte->__toString());
        $this->assertEquals('1.5 kB', $byte->toString());
        
        // Test humanize with different precisions and delimiters
        $this->assertEquals('1.5 kB', Byte::humanize(1536));
        $this->assertEquals('1.500 kB', Byte::humanize(1536, 3));
        $this->assertEquals('1.5_kB', Byte::humanize(1536, 2, '_'));
    }
    
    /**
     * Test unit conversion methods
     */
    public function testUnitConversion(): void
    {
        $byte = new Byte(1536);
        
        // Test to() method
        $this->assertEquals('1.5 kB', $byte->to('kb'));
        $this->assertEquals('1.500 kB', $byte->to('kb', 3));
        $this->assertEquals('1.5_kB', $byte->to('kb', 2, '_'));
        
        // Test convenience methods
        $this->assertEquals('1.5 kB', $byte->toKb());
        $this->assertEquals('0 MB', $byte->toMb(0));
        $this->assertEquals('0.00 GB', $byte->toGb(2));
        
        // Test getValue method
        $this->assertEquals(1.5, $byte->getValue('kb'));
        $this->assertEquals(0.0015, $byte->getValue('mb', 4));
    }
    
    /**
     * Test parse method
     */
    public function testParse(): void
    {
        // Test numeric parsing
        $this->assertEquals(1024, Byte::parse(1024));
        
        // Test Byte instance parsing
        $byte = new Byte(2048);
        $this->assertEquals(2048, Byte::parse($byte));
        
        // Test string parsing
        $this->assertEquals(1024, Byte::parse('1 kB'));
        $this->assertEquals(1048576, Byte::parse('1 MB'));
        $this->assertEquals(1073741824, Byte::parse('1 GB'));
        
        // Test string parsing with different formats
        $this->assertEquals(1024, Byte::parse('1kB'));
        $this->assertEquals(1024, Byte::parse('1 kB'));
        $this->assertEquals(1024, Byte::parse('1    kB'));
        
        // Test invalid formats
        $this->expectException(\InvalidArgumentException::class);
        Byte::parse('invalid');
    }
    
    /**
     * Test comparison methods with single values
     */
    public function testSingleValueComparison(): void
    {
        $byte = new Byte(1024); // 1 KB
        
        // Test compare method
        $this->assertEquals(Byte::COMPARE_EQ, $byte->compare(1024));
        $this->assertEquals(Byte::COMPARE_EQ, $byte->compare('1 kB'));
        $this->assertEquals(Byte::COMPARE_GT, $byte->compare(512));
        $this->assertEquals(Byte::COMPARE_LT, $byte->compare(2048));
        
        // Test equalTo method
        $this->assertTrue($byte->equalTo(1024));
        $this->assertTrue($byte->equalTo('1 kB'));
        $this->assertFalse($byte->equalTo(2048));
        
        // Test lessThan method
        $this->assertTrue($byte->lessThan(2048));
        $this->assertTrue($byte->lessThan('2 kB'));
        $this->assertFalse($byte->lessThan(1024));
        $this->assertFalse($byte->lessThan(512));
        
        // Test greaterThan method
        $this->assertTrue($byte->greaterThan(512));
        $this->assertTrue($byte->greaterThan('0.5 kB'));
        $this->assertFalse($byte->greaterThan(1024));
        $this->assertFalse($byte->greaterThan(2048));
        
        // Test new comparison methods
        $this->assertTrue($byte->lessThanOrEqual(1024));
        $this->assertTrue($byte->lessThanOrEqual(2048));
        $this->assertFalse($byte->lessThanOrEqual(512));
        
        $this->assertTrue($byte->greaterThanOrEqual(1024));
        $this->assertTrue($byte->greaterThanOrEqual(512));
        $this->assertFalse($byte->greaterThanOrEqual(2048));
        
        $this->assertTrue($byte->between(512, 2048));
        $this->assertTrue($byte->between(1024, 2048));
        $this->assertFalse($byte->between(2048, 4096));
    }
    
    /**
     * Test comparison methods with arrays and modes
     */
    public function testArrayComparison(): void
    {
        $byte = new Byte(1024); // 1 KB
        
        // Test compare with arrays
        $this->assertEquals(Byte::COMPARE_EQ, $byte->compare([1024, '1 kB'], Byte::MODE_ALL));
        $this->assertFalse($byte->compare([1024, 2048], Byte::MODE_ALL));
        $this->assertEquals(Byte::COMPARE_EQ, $byte->compare([512, 1024], Byte::MODE_ANY));
        
        // Test equalTo with arrays
        $this->assertTrue($byte->equalTo([1024, '1 kB'], Byte::MODE_ALL));
        $this->assertFalse($byte->equalTo([1024, 2048], Byte::MODE_ALL));
        $this->assertTrue($byte->equalTo([512, 1024], Byte::MODE_ANY));
        $this->assertFalse($byte->equalTo([512, 2048], Byte::MODE_ANY));
        
        // Test lessThan with arrays
        $this->assertTrue($byte->lessThan([2048, 4096], Byte::MODE_ALL));
        $this->assertFalse($byte->lessThan([512, 2048], Byte::MODE_ALL));
        $this->assertTrue($byte->lessThan([512, 2048], Byte::MODE_ANY));
        
        // Test greaterThan with arrays
        $this->assertTrue($byte->greaterThan([512, 768], Byte::MODE_ALL));
        $this->assertFalse($byte->greaterThan([512, 2048], Byte::MODE_ALL));
        $this->assertTrue($byte->greaterThan([512, 2048], Byte::MODE_ANY));
        
        // Test in ranges
        $this->assertTrue($byte->inRanges([[512, 2048], [3072, 4096]]));
        $this->assertFalse($byte->inRanges([[2048, 3072], [4096, 5120]]));
    }
    
    /**
     * Test arithmetic operations
     */
    public function testArithmetic(): void
    {
        $byte = new Byte(1024); // 1 KB
        
        // Test increment
        $result = $byte->increment(1024);
        $this->assertEquals(2048, $result->value);
        $this->assertEquals(1024, $byte->value); // Original should be unchanged
        
        // Test decrement
        $result = $byte->decrement(512);
        $this->assertEquals(512, $result->value);
        
        // Test decrement with exception
        $this->expectException(\LogicException::class);
        $byte->decrement(2048);
    }
    
    /**
     * Test more arithmetic operations
     */
    public function testMoreArithmetic(): void
    {
        $byte = new Byte(1024); // 1 KB
        
        // Test divide
        $result = $byte->divide(2);
        $this->assertEquals(512, $result->value);
        
        // Test multiply
        $result = $byte->multiply(2);
        $this->assertEquals(2048, $result->value);
        
        // Test modulo
        $result = $byte->modulo(400);
        $this->assertEquals(224, $result->value); // 1024 % 400 = 224
        
        // Test abs
        $byte = new Byte(-1024);
        $result = $byte->abs();
        $this->assertEquals(1024, $result->value);
        
        // Test min/max
        $byte = new Byte(1024);
        $result = $byte->min([512, 2048]);
        $this->assertEquals(512, $result->value);
        
        $result = $byte->max([512, 2048]);
        $this->assertEquals(2048, $result->value);
    }
    
    /**
     * Test division by zero exception
     */
    public function testDivisionByZero(): void
    {
        $byte = new Byte(1024);
        
        $this->expectException(\DivisionByZeroError::class);
        $byte->divide(0);
    }
    
    /**
     * Test modulo with zero exception
     */
    public function testModuloByZero(): void
    {
        $byte = new Byte(1024);
        
        $this->expectException(\DivisionByZeroError::class);
        $byte->modulo(0);
    }
    
    /**
     * Test static collection methods
     */
    public function testStaticCollectionMethods(): void
    {
        // Test range
        $range = Byte::range('1 kB', '5 kB', '1 kB');
        $this->assertCount(5, $range);
        $this->assertEquals(1024, $range[0]->value);
        $this->assertEquals(5120, $range[4]->value);
        
        // Test sum
        $sum = Byte::sum(['1 kB', '2 kB', '3 kB']);
        $this->assertEquals(6144, $sum->value);
        
        // Test average
        $avg = Byte::average(['1 kB', '2 kB', '3 kB']);
        $this->assertEquals(2048, $avg->value);
        
        // Test maximum
        $max = Byte::maximum(['1 kB', '5 kB', '3 kB']);
        $this->assertEquals(5120, $max->value);
        
        // Test minimum
        $min = Byte::minimum(['1 kB', '5 kB', '3 kB']);
        $this->assertEquals(1024, $min->value);
    }
    
    /**
     * Test bit conversion
     */
    public function testBitConversion(): void
    {
        $byte = new Byte(1024);
        
        // Convert to bits
        $this->assertEquals(8192, $byte->toBits());
        
        // Test from bits
        $byte = Byte::fromBits(8192);
        $this->assertEquals(1024, $byte->value);
    }
    
    /**
     * Test transfer time calculation
     */
    public function testTransferTimeCalculation(): void
    {
        // 1 MB file with 1 MB/s bandwidth
        $fileSize = Byte::mb(1);
        $bandwidth = Byte::mb(1);
        
        // Should take 1 second
        $this->assertEquals(1, $fileSize->getTransferTime($bandwidth));
        
        // Test formatted time for various scenarios
        $this->assertEquals("1 second", $fileSize->getFormattedTransferTime($bandwidth));
        
        // 10 MB file with 1 MB/s bandwidth
        $fileSize = Byte::mb(10);
        $this->assertEquals("10 seconds", $fileSize->getFormattedTransferTime($bandwidth));
        
        // 1 GB file with 10 MB/s bandwidth
        $fileSize = Byte::gb(1);
        $bandwidth = Byte::mb(10);
        $this->assertEquals("1 minute, 42 seconds", $fileSize->getFormattedTransferTime($bandwidth));
        
        // 1 TB file with 10 MB/s bandwidth
        $fileSize = Byte::tb(1);
        $this->assertEquals("1 day, 3 hours", $fileSize->getFormattedTransferTime($bandwidth));
    }
    
    /**
     * Test state checking methods
     */
    public function testStateChecking(): void
    {
        // Test zero
        $byte = new Byte(0);
        $this->assertTrue($byte->isZero());
        $this->assertFalse($byte->isPositive());
        $this->assertFalse($byte->isNegative());
        
        // Test positive
        $byte = new Byte(1024);
        $this->assertFalse($byte->isZero());
        $this->assertTrue($byte->isPositive());
        $this->assertFalse($byte->isNegative());
        
        // Test negative
        $byte = new Byte(-1024);
        $this->assertFalse($byte->isZero());
        $this->assertFalse($byte->isPositive());
        $this->assertTrue($byte->isNegative());
    }
    
    /**
     * Test invalid range parameters
     */
    public function testInvalidRangeParameters(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Byte::range('2 kB', '1 kB', '1 kB');
    }
    
    /**
     * Test zero step in range
     */
    public function testZeroStepInRange(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Byte::range('1 kB', '5 kB', 0);
    }
    
    /**
     * Test empty array for average
     */
    public function testEmptyArrayForAverage(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Byte::average([]);
    }
    
    /**
     * Test empty array for maximum
     */
    public function testEmptyArrayForMaximum(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Byte::maximum([]);
    }
    
    /**
     * Test empty array for minimum
     */
    public function testEmptyArrayForMinimum(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Byte::minimum([]);
    }
    
    /**
     * Test zero or negative bandwidth for transfer time
     */
    public function testInvalidBandwidth(): void
    {
        $byte = new Byte(1024);
        
        $this->expectException(\InvalidArgumentException::class);
        $byte->getTransferTime(0);
    }
}
