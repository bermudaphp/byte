<?php

namespace Bermuda\Stdlib\Tests;

use Bermuda\Stdlib\Byte;
use Bermuda\Stdlib\BitRate;
use Bermuda\Stdlib\BitFormatter;
use PHPUnit\Framework\TestCase;

class IntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        // Ensure we have default languages available for testing
        try {
            BitFormatter::loadDefaults();
        } catch (\Exception $e) {
            // In case loadDefaults is not available, we'll add a basic English translation
            BitFormatter::addLanguage('en', [
                'time' => [
                    'format' => '{value} {unit}',
                    'separator' => ', ',
                    'less_than_second' => 'less than a second',
                    'second' => 'second',
                    'seconds' => 'seconds',
                    'minute' => 'minute',
                    'minutes' => 'minutes',
                    'hour' => 'hour',
                    'hours' => 'hours',
                    'day' => 'day',
                    'days' => 'days',
                ],
            ]);
        }
    }
    
    /**
     * @test
     */
    public function downloadTimeScenario(): void
    {
        // Scenario: Calculate download time for a large file
        
        // Given a file size of 4 GB
        $fileSize = Byte::gb(4);
        $this->assertEquals('4 GB', $fileSize->toString());
        
        // And a download speed of 100 Mbps
        $downloadSpeed = BitRate::mbps(100);
        $this->assertEquals('100 Mbps', $downloadSpeed->toString());
        
        // When I calculate the transfer time
        $seconds = $downloadSpeed->calculateTransferTime($fileSize);
        
        // Then I should get the correct time in seconds (4 GB = 32 Gb, at 100 Mbps = 320 seconds)
        $this->assertEquals(320, $seconds);
        
        // And the formatted time should be human-readable
        $formattedTime = $downloadSpeed->getFormattedTransferTime($fileSize);
        $this->assertEquals('5 minutes, 20 seconds', $formattedTime);
        
        // And I can get the same result using the BitFormatter directly
        $formattedTime2 = BitFormatter::formatTime($seconds);
        $this->assertEquals('5 minutes, 20 seconds', $formattedTime2);
    }
    
    /**
     * @test
     */
    public function videoStreamingSizeScenario(): void
    {
        // Scenario: Estimate video file size from streaming rate and duration
        
        // Given a video bitrate of 10 Mbps (high-quality stream)
        $streamRate = BitRate::mbps(10);
        $this->assertEquals('10 Mbps', $streamRate->toString());
        
        // And a video duration of 2 hours
        $durationInSeconds = 2 * 60 * 60; // 7200 seconds
        
        // When I estimate the file size
        $fileSize = $streamRate->estimateFileSize($durationInSeconds);
        
        // Then I should get the correct size (10 Mbps for 7200 seconds = 9 GB)
        $this->assertEquals(9_000_000_000, $fileSize->value);
        $this->assertEquals('9 GB', $fileSize->toString());
        
        // And I can get the same result using the BitFormatter directly
        $fileSize2 = BitFormatter::estimateFileSize($streamRate, $durationInSeconds);
        $this->assertEquals('9 GB', $fileSize2->toString());
    }
    
    /**
     * @test
     */
    public function networkCapacityScenario(): void
    {
        // Scenario: Calculate network capacity requirements for multiple users
        
        // Given 50 users each requiring 5 Mbps of bandwidth
        $usersCount = 50;
        $perUserRate = BitRate::mbps(5);
        
        // When I calculate the total required bandwidth
        $totalRequired = $perUserRate->multiply($usersCount);
        
        // Then I should get 250 Mbps
        $this->assertEquals(250_000_000, $totalRequired->value);
        $this->assertEquals('250 Mbps', $totalRequired->toString());
        
        // Given a network capacity of 1 Gbps
        $capacity = BitRate::gbps(1);
        
        // When I check if the capacity is sufficient
        $isCapacitySufficient = $capacity->greaterThan($totalRequired);
        
        // Then it should be true
        $this->assertTrue($isCapacitySufficient);
        
        // And I can calculate the capacity utilization percentage
        $utilizationRatio = $totalRequired->value / $capacity->value;
        $utilizationPercentage = $utilizationRatio * 100;
        
        // Which should be 25%
        $this->assertEquals(25, $utilizationPercentage);
    }
    
    /**
     * @test
     */
    public function transferAmountScenario(): void
    {
        // Scenario: Calculate how much data can be transferred in a given time
        
        // Given a download speed of 50 Mbps
        $downloadSpeed = BitRate::mbps(50);
        
        // And a time period of 30 minutes
        $minutes = 30;
        $seconds = $minutes * 60; // 1800 seconds
        
        // When I calculate the amount of data that can be transferred
        $dataAmount = $downloadSpeed->calculateTransferAmount($seconds);
        
        // Then I should get approximately 11.25 GB
        // 50 Mbps = 6.25 MBps, for 1800 seconds = 11250 MB = 11.25 GB
        $expectedBytes = 11_250_000_000; // 11.25 GB in bytes
        $this->assertEquals($expectedBytes, $dataAmount->value, '', 0.01 * $expectedBytes);
        
        // And the formatted size should be human-readable
        $this->assertEquals('11.25 GB', $dataAmount->toString());
        
        // And I can get the same result using the BitFormatter directly
        $dataAmount2 = BitFormatter::calculateTransferAmount($downloadSpeed, $seconds);
        $this->assertEquals('11.25 GB', $dataAmount2->toString());
    }
    
    /**
     * @test
     */
    public function multiLanguageFormattingScenario(): void
    {
        // Skip this test if we don't have multiple languages loaded
        $languages = BitFormatter::getLoadedLanguages();
        if (count($languages) < 2) {
            $this->markTestSkipped('Not enough languages loaded for multi-language test');
        }
        
        // Scenario: Format transfer times in different languages
        
        // Given a file size of 2 GB
        $fileSize = Byte::gb(2);
        
        // And a download speed of 25 Mbps
        $downloadSpeed = BitRate::mbps(25);
        
        // When I calculate the transfer time
        $seconds = $downloadSpeed->calculateTransferTime($fileSize);
        // 2 GB = 16 Gb, at 25 Mbps = 640 seconds
        $this->assertEquals(640, $seconds);
        
        // Then I can format it in different languages
        foreach ($languages as $language) {
            $formatted = BitFormatter::formatTime($seconds, $language);
            $this->assertIsString($formatted);
            $this->assertNotEmpty($formatted);
            
            // Also test through the BitRate class
            $formatted2 = $downloadSpeed->getFormattedTransferTime($fileSize, $language);
            $this->assertEquals($formatted, $formatted2);
            
            // And through the Byte class
            $formatted3 = $fileSize->getFormattedTransferTime($downloadSpeed, $language);
            $this->assertEquals($formatted, $formatted3);
        }
    }
    
    /**
     * @test
     */
    public function conversionsBetweenUnitsScenario(): void
    {
        // Scenario: Convert between different data size and rate units
        
        // Data size conversions
        $data = Byte::gb(1.5);
        $this->assertEquals(1.5 * pow(1024, 3), $data->value);
        $this->assertEquals('1.5 GB', $data->toString());
        $this->assertEquals('1536 MB', $data->toMb(0));
        $this->assertEquals('1572864 kB', $data->toKb(0));
        
        // Data rate conversions - bit based
        $rate = BitRate::gbps(1);
        $this->assertEquals(1_000_000_000, $rate->value);
        $this->assertEquals('1 Gbps', $rate->toString());
        $this->assertEquals('1000 Mbps', $rate->toMbps(0));
        $this->assertEquals('1000000 kbps', $rate->toKbps(0));
        
        // Data rate conversions - byte based
        $this->assertEquals('125 MBps', $rate->toMBps(0));
        $this->assertEquals('125000 kBps', $rate->toKBps(0));
        
        // Convert between bit and byte based rates
        $bitRate = BitRate::mbps(80);
        $byteEquivalent = $bitRate->toMBps();
        $this->assertEquals('10 MBps', $byteEquivalent);
        
        $byteRate = BitRate::mBps(10);
        $bitEquivalent = $byteRate->toMbps();
        $this->assertEquals('80 Mbps', $bitEquivalent);
        
        // Create from one unit type and convert to another
        $rate = BitRate::from(50, 'Mbps');
        $this->assertEquals('50 Mbps', $rate->toString());
        $this->assertEquals('6.25 MBps', $rate->toString('byte'));
    }
}
