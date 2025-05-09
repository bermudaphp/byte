<?php

namespace Bermuda\Stdlib\Tests;

use Bermuda\Stdlib\Byte;
use Bermuda\Stdlib\BitRate;
use PHPUnit\Framework\TestCase;

class BitRateTest extends TestCase
{
    /**
     * @test
     */
    public function constructorCreatesValidInstance(): void
    {
        // Numeric value (bits per second)
        $rate = new BitRate(1_000_000);
        $this->assertEquals(1_000_000, $rate->value);
        
        // String value
        $rate = new BitRate('1 Mbps');
        $this->assertEquals(1_000_000, $rate->value);
        
        // BitRate instance
        $original = new BitRate(5_000_000);
        $rate = new BitRate($original);
        $this->assertEquals(5_000_000, $rate->value);
    }
    
    /**
     * @test
     */
    public function staticFactoryMethodsCreateValidInstances(): void
    {
        // Bit-based factories
        $this->assertEquals(1000, BitRate::bps(1000)->value);
        $this->assertEquals(1000 * 1000, BitRate::kbps(1000)->value);
        $this->assertEquals(10 * 1000 * 1000, BitRate::mbps(10)->value);
        $this->assertEquals(1 * 1000 * 1000 * 1000, BitRate::gbps(1)->value);
        $this->assertEquals(2 * pow(1000, 4), BitRate::tbps(2)->value);
        
        // Byte-based factories (converts to bits internally)
        $this->assertEquals(8000, BitRate::bytesPerSec(1000)->value);
        $this->assertEquals(8 * 1000 * 1000, BitRate::kBps(1000)->value);
        $this->assertEquals(8 * 10 * 1000 * 1000, BitRate::mBps(10)->value);
        $this->assertEquals(8 * 1 * 1000 * 1000 * 1000, BitRate::gBps(1)->value);
        
        // Named factories
        $rate = BitRate::from(10, 'Mbps');
        $this->assertEquals(10 * 1000 * 1000, $rate->value);
        $rate = BitRate::from(1.5, 'GBps');
        $this->assertEquals(1.5 * 8 * 1000 * 1000 * 1000, $rate->value);
        
        // Human-readable string
        $rate = BitRate::fromHumanReadable('10 Mbps');
        $this->assertEquals(10 * 1000 * 1000, $rate->value);
    }
    
    /**
     * @test
     */
    public function conversionMethodsWorkCorrectly(): void
    {
        $rate = BitRate::mbps(100);  // 100 Mbps
        
        // Bits and bytes
        $this->assertEquals(100_000_000, $rate->toBits());
        $this->assertEquals(12_500_000, $rate->toBytes());
        
        // To string formats
        $this->assertEquals('100 Mbps', $rate->toString());
        $this->assertEquals('12.5 MBps', $rate->toString('byte'));
        
        // Specific unit formatters
        $this->assertEquals('100 Mbps', $rate->toMbps());
        $this->assertEquals('0.1 Gbps', $rate->toGbps());
        $this->assertEquals('12.5 MBps', $rate->toMBps());
        $this->assertEquals('12500 kBps', $rate->toKBps());
        
        // Custom output format
        $this->assertEquals('100.000_Mbps', $rate->to('Mbps', 3, '_'));
        
        // Get raw values
        $this->assertEquals(100, $rate->getValue('Mbps'));
        $this->assertEquals(12.5, $rate->getValue('MBps'));
    }
    
    /**
     * @test
     */
    public function comparisonMethodsWorkCorrectly(): void
    {
        $rate = BitRate::mbps(100);  // 100 Mbps
        
        // Equality
        $this->assertTrue($rate->equalTo(BitRate::kbps(100000)));
        $this->assertTrue($rate->equalTo('100 Mbps'));
        $this->assertTrue($rate->equalTo('12.5 MBps'));
        
        // Greater/less than
        $this->assertTrue($rate->greaterThan(BitRate::mbps(50)));
        $this->assertTrue($rate->lessThan(BitRate::gbps(1)));
        $this->assertTrue($rate->greaterThanOrEqual(BitRate::mbps(100)));
        $this->assertTrue($rate->lessThanOrEqual(BitRate::mbps(100)));
        
        // Array comparisons
        $this->assertTrue($rate->greaterThan(['10 Mbps', '50 Mbps'], BitRate::MODE_ALL));
        $this->assertTrue($rate->greaterThan(['10 Mbps', '150 Mbps'], BitRate::MODE_ANY));
        $this->assertFalse($rate->greaterThan(['150 Mbps', '200 Mbps'], BitRate::MODE_ANY));
    }
    
    /**
     * @test
     */
    public function arithmeticOperationsWorkCorrectly(): void
    {
        $rate = BitRate::mbps(100);
        
        // Addition
        $result = $rate->increment(BitRate::mbps(50));
        $this->assertEquals(150_000_000, $result->value);
        
        // Subtraction
        $result = $rate->decrement(BitRate::mbps(30));
        $this->assertEquals(70_000_000, $result->value);
        
        // Multiplication
        $result = $rate->multiply(2);
        $this->assertEquals(200_000_000, $result->value);
        
        // Division
        $result = $rate->divide(4);
        $this->assertEquals(25_000_000, $result->value);
        
        // Throttling
        $result = $rate->throttle(0.8);
        $this->assertEquals(80_000_000, $result->value);
    }
    
    /**
     * @test
     */
    public function transferCalculationsWorkCorrectly(): void
    {
        $rate = BitRate::mbps(100);      // 100 Mbps download speed
        $fileSize = Byte::gb(1);         // 1 GB file
        
        // Calculate transfer time (1 GB = 8 Gb, at 100 Mbps = 80 seconds)
        $seconds = $rate->calculateTransferTime($fileSize);
        $this->assertEquals(80, $seconds);
        
        // Calculate transfer amount (100 Mbps for 60 seconds = 750 MB)
        $downloadedSize = $rate->calculateTransferAmount(60);
        $this->assertEquals(750_000_000, $downloadedSize->value);
        
        // Estimate file size (5 Mbps for 3600 seconds = 2.25 GB)
        $streamingRate = BitRate::mbps(5);
        $videoSize = $streamingRate->estimateFileSize(3600);
        $this->assertEquals(2_250_000_000, $videoSize->value);
    }
    
    /**
     * @test
     */
    public function staticOperationsOnCollectionsWorkCorrectly(): void
    {
        $rates = [
            BitRate::mbps(10),
            BitRate::mbps(50),
            BitRate::mbps(100)
        ];
        
        // Sum
        $sum = BitRate::sum($rates);
        $this->assertEquals(160_000_000, $sum->value);
        
        // Average (53.33 Mbps)
        $avg = BitRate::average($rates);
        $this->assertEquals(round(160_000_000 / 3), round($avg->value));
        
        // Maximum
        $max = BitRate::maximum($rates);
        $this->assertEquals(100_000_000, $max->value);
        
        // Minimum
        $min = BitRate::minimum($rates);
        $this->assertEquals(10_000_000, $min->value);
        
        // Range
        $range = BitRate::range(BitRate::mbps(10), BitRate::mbps(30), BitRate::mbps(10));
        $this->assertCount(3, $range);
        $this->assertEquals(10_000_000, $range[0]->value);
        $this->assertEquals(20_000_000, $range[1]->value);
        $this->assertEquals(30_000_000, $range[2]->value);
    }
    
    /**
     * @test
     */
    public function toStringMethodFormatsOutputCorrectly(): void
    {
        $rate = BitRate::mbps(100);
        $this->assertEquals('100 Mbps', (string)$rate);
    }
    
    /**
     * @test
     */
    public function stateCheckingMethodsWorkCorrectly(): void
    {
        $zero = new BitRate(0);
        $positive = new BitRate(1000);
        
        $this->assertTrue($zero->isZero());
        $this->assertFalse($positive->isZero());
        
        $this->assertTrue($positive->isPositive());
        $this->assertFalse($zero->isPositive());
        
        $this->assertEquals(1000, $positive->abs()->value);
    }
    
    /**
     * @test
     */
    public function getUnitTypeIdentifiesUnitTypesCorrectly(): void
    {
        // Test by using the to() method which uses getUnitType internally
        $rate = BitRate::mbps(100);
        
        // Bit-based units
        $this->assertEquals('100 Mbps', $rate->to('Mbps'));
        $this->assertEquals('100000 kbps', $rate->to('kbps'));
        $this->assertEquals('0.1 Gbps', $rate->to('Gbps'));
        
        // Byte-based units
        $this->assertEquals('12.5 MBps', $rate->to('MBps'));
        $this->assertEquals('12500 kBps', $rate->to('kBps'));
        $this->assertEquals('0.0125 GBps', $rate->to('GBps'));
    }
    
    /**
     * @test
     */
    public function parseMethodHandlesVariousInputFormats(): void
    {
        // Test through factory methods
        
        // Numeric values
        $this->assertEquals(1000, BitRate::new(1000)->value);
        
        // BitRate instance
        $original = new BitRate(5000);
        $this->assertEquals(5000, BitRate::new($original)->value);
        
        // String values with bit-based units
        $this->assertEquals(1000, BitRate::new('1000 bps')->value);
        $this->assertEquals(1_000_000, BitRate::new('1 Mbps')->value);
        $this->assertEquals(1_500_000, BitRate::new('1.5 Mbps')->value);
        
        // String values with byte-based units
        $this->assertEquals(8000, BitRate::new('1000 Bps')->value);
        $this->assertEquals(8_000_000, BitRate::new('1 MBps')->value);
        $this->assertEquals(120_000_000, BitRate::new('15 MBps')->value);
    }
    
    /**
     * @test
     */
    public function throwsExceptionsForInvalidInputs(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        BitRate::new('invalid string');
        
        $this->expectException(\InvalidArgumentException::class);
        BitRate::new('1.5 InvalidUnit');
        
        $this->expectException(\LogicException::class);
        $rate = new BitRate(10);
        $rate->decrement(20);
        
        $this->expectException(\DivisionByZeroError::class);
        $rate = new BitRate(100);
        $rate->divide(0);
        
        $this->expectException(\InvalidArgumentException::class);
        $rate = new BitRate(0);
        $rate->calculateTransferTime(Byte::mb(1));
    }
}
