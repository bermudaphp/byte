<?php

namespace Bermuda\Stdlib\Tests;

use Bermuda\Stdlib\Byte;
use PHPUnit\Framework\TestCase;

class ByteTest extends TestCase
{
    /**
     * @test
     */
    public function constructorCreatesValidInstance(): void
    {
        // Numeric value
        $byte = new Byte(1024);
        $this->assertEquals(1024, $byte->value);
        
        // String value
        $byte = new Byte('1024 kB');
        $this->assertEquals(1024 * 1024, $byte->value);
        
        // Byte instance
        $original = new Byte(512);
        $byte = new Byte($original);
        $this->assertEquals(512, $byte->value);
    }
    
    /**
     * @test
     */
    public function staticFactoryMethodsCreateValidInstances(): void
    {
        // new() method
        $byte = Byte::new(1024);
        $this->assertEquals(1024, $byte->value);
        
        // Size units methods
        $this->assertEquals(1024, Byte::b(1024)->value);
        $this->assertEquals(1024 * 1024, Byte::kb(1024)->value);
        $this->assertEquals(50 * 1024 * 1024, Byte::mb(50)->value);
        $this->assertEquals(2 * 1024 * 1024 * 1024, Byte::gb(2)->value);
        $this->assertEquals(3 * pow(1024, 4), Byte::tb(3)->value);
        $this->assertEquals(1 * pow(1024, 5), Byte::pb(1)->value);
        $this->assertEquals(1 * pow(1024, 6), Byte::eb(1)->value);
        $this->assertEquals(1 * pow(1024, 7), Byte::zb(1)->value);
        $this->assertEquals(1 * pow(1024, 8), Byte::yb(1)->value);
        
        // fromHumanReadable method
        $byte = Byte::fromHumanReadable('2.5 GB');
        $this->assertEquals(2.5 * pow(1024, 3), $byte->value);
        
        // fromBits method
        $byte = Byte::fromBits(8192);
        $this->assertEquals(1024, $byte->value);
        
        // from method
        $byte = Byte::from(5, 'GB');
        $this->assertEquals(5 * pow(1024, 3), $byte->value);
    }
    
    /**
     * @test
     */
    public function toStringReturnsHumanizedValue(): void
    {
        $byte = new Byte(1536);
        $this->assertEquals('1.5 kB', (string)$byte);
        $this->assertEquals('1.5 kB', $byte->toString());
    }
    
    /**
     * @test
     */
    public function unitConversionMethodsReturnCorrectStrings(): void
    {
        $byte = new Byte(1536);
        
        $this->assertEquals('1.5 kB', $byte->toKb());
        $this->assertEquals('0 MB', $byte->toMb(0));
        $this->assertEquals('0.0015 MB', $byte->toMb());
        $this->assertEquals('0.000001 GB', $byte->toGb());
        
        // Test custom precision and delimiter
        $this->assertEquals('1.500_kB', $byte->to('kb', 3, '_'));
        
        // Test raw value in specific unit
        $this->assertEquals(1.5, $byte->getValue('kb'));
        $this->assertEquals(1.5, $byte->getValue('kB'));
        $this->assertEquals(0.00146484375, $byte->getValue('MB'));
    }
    
    /**
     * @test
     */
    public function comparisonMethodsWorkCorrectly(): void
    {
        $byte = Byte::kb(1024); // 1 MB
        
        // Single-value comparisons
        $this->assertTrue($byte->equalTo('1 MB'));
        $this->assertTrue($byte->greaterThan('900 kB'));
        $this->assertTrue($byte->lessThan('1.1 MB'));
        $this->assertTrue($byte->lessThanOrEqual('1 MB'));
        $this->assertTrue($byte->greaterThanOrEqual('1 MB'));
        $this->assertTrue($byte->between('900 kB', '1.1 MB'));
        
        // Multiple-value comparisons - MODE_ANY
        $this->assertTrue($byte->greaterThan(['900 kB', '1.1 MB'], Byte::MODE_ANY));
        $this->assertFalse($byte->greaterThan(['1.1 MB', '2 MB'], Byte::MODE_ANY));
        
        // Multiple-value comparisons - MODE_ALL
        $this->assertTrue($byte->greaterThan(['500 kB', '800 kB'], Byte::MODE_ALL));
        $this->assertFalse($byte->greaterThan(['900 kB', '1.1 MB'], Byte::MODE_ALL));
        
        // Range testing
        $this->assertTrue($byte->inRanges([['500 kB', '800 kB'], ['1 MB', '1.5 MB']]));
        $this->assertFalse($byte->inRanges([['500 kB', '800 kB'], ['2 MB', '3 MB']]));
    }
    
    /**
     * @test
     */
    public function arithmeticOperationsWorkCorrectly(): void
    {
        $byte = Byte::mb(1); // 1 MB
        
        // Addition
        $result = $byte->increment('500 kB');
        $this->assertEquals(Byte::kb(1024 + 500)->value, $result->value);
        
        // Subtraction
        $result = $byte->decrement('512 kB');
        $this->assertEquals(Byte::kb(1024 - 512)->value, $result->value);
        
        // Division
        $result = $byte->divide(2);
        $this->assertEquals(Byte::kb(512)->value, $result->value);
        
        // Multiplication
        $result = $byte->multiply(3);
        $this->assertEquals(Byte::mb(3)->value, $result->value);
        
        // Modulo
        $result = $byte->modulo('512 kB');
        $this->assertEquals(0, $result->value);
        
        // Absolute value
        $result = (new Byte(-1024))->abs();
        $this->assertEquals(1024, $result->value);
        
        // Min/Max
        $result = $byte->max('1.5 MB');
        $this->assertEquals(Byte::mb(1.5)->value, $result->value);
        
        $result = $byte->min(['2 MB', '500 kB']);
        $this->assertEquals(Byte::kb(500)->value, $result->value);
    }
    
    /**
     * @test
     */
    public function staticOperationsOnCollectionsWorkCorrectly(): void
    {
        // Range
        $range = Byte::range('1 MB', '3 MB', '1 MB');
        $this->assertCount(3, $range);
        $this->assertEquals(Byte::mb(1)->value, $range[0]->value);
        $this->assertEquals(Byte::mb(2)->value, $range[1]->value);
        $this->assertEquals(Byte::mb(3)->value, $range[2]->value);
        
        // Sum
        $sum = Byte::sum(['1 MB', '2 MB', '500 kB']);
        $this->assertEquals(Byte::kb(1024 + 2048 + 500)->value, $sum->value);
        
        // Average
        $avg = Byte::average(['1 MB', '2 MB', '3 MB']);
        $this->assertEquals(Byte::mb(2)->value, $avg->value);
        
        // Maximum
        $max = Byte::maximum(['1 MB', '500 kB', '2 GB']);
        $this->assertEquals(Byte::gb(2)->value, $max->value);
        
        // Minimum
        $min = Byte::minimum(['1 MB', '500 kB', '2 GB']);
        $this->assertEquals(Byte::kb(500)->value, $min->value);
    }
    
    /**
     * @test
     */
    public function bitConversionMethods(): void
    {
        $byte = new Byte(1024);
        
        // To bits
        $this->assertEquals(8192, $byte->toBits());
        
        // From bits
        $byteFromBits = Byte::fromBits(8192);
        $this->assertEquals(1024, $byteFromBits->value);
    }
    
    /**
     * @test
     */
    public function transferTimeCalculation(): void
    {
        $fileSize = Byte::gb(1);
        $bandwidth = 10 * 1024 * 1024; // 10 MB/s
        
        // Get transfer time in seconds (without BitRate)
        $seconds = $fileSize->getTransferTime($bandwidth);
        // 1 GB = 1024 MB, at 10 MB/s = 102.4 seconds
        $this->assertEquals(102.4, $seconds);
    }
    
    /**
     * @test
     */
    public function humanizeMethodFormatsBytesCorrectly(): void
    {
        $this->assertEquals('1.5 kB', Byte::humanize(1536));
        $this->assertEquals('1.500 kB', Byte::humanize(1536, 3));
        $this->assertEquals('1.5_kB', Byte::humanize(1536, 2, '_'));
        $this->assertEquals('1 MB', Byte::humanize(1024 * 1024, 0));
    }
    
    /**
     * @test
     */
    public function stateCheckingMethodsWorkCorrectly(): void
    {
        $zero = new Byte(0);
        $positive = new Byte(1024);
        $negative = new Byte(-1024);
        
        $this->assertTrue($zero->isZero());
        $this->assertFalse($positive->isZero());
        
        $this->assertTrue($positive->isPositive());
        $this->assertFalse($zero->isPositive());
        $this->assertFalse($negative->isPositive());
        
        $this->assertTrue($negative->isNegative());
        $this->assertFalse($zero->isNegative());
        $this->assertFalse($positive->isNegative());
    }
    
    /**
     * @test
     */
    public function parseFunctionHandlesVariousInputs(): void
    {
        // Test the internal parse function through a factory method
        
        // Numeric value
        $this->assertEquals(1024, Byte::new(1024)->value);
        
        // Byte instance
        $original = new Byte(512);
        $this->assertEquals(512, Byte::new($original)->value);
        
        // String with valid units
        $this->assertEquals(1536, Byte::new('1.5 kB')->value);
        $this->assertEquals(1536, Byte::new('1.5 KB')->value);
        $this->assertEquals(1024 * 1024, Byte::new('1 MB')->value);
        $this->assertEquals(1024 * 1024 * 1024, Byte::new('1 GB')->value);
    }
    
    /**
     * @test
     */
    public function throwsExceptionsForInvalidInputs(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Byte::new('invalid string');
        
        $this->expectException(\InvalidArgumentException::class);
        Byte::new('1.5 InvalidUnit');
        
        $this->expectException(\LogicException::class);
        $byte = new Byte(10);
        $byte->decrement(20);
        
        $this->expectException(\DivisionByZeroError::class);
        $byte = new Byte(100);
        $byte->divide(0);
    }
}
