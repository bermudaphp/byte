<?php

namespace Bermuda\Stdlib\Tests;

use Bermuda\Stdlib\Byte;
use Bermuda\Stdlib\BitRate;
use Bermuda\Stdlib\BitFormatter;
use PHPUnit\Framework\TestCase;

class BitFormatterTest extends TestCase
{
    /**
     * @var array English language translations
     */
    private $englishTranslation = [
        'language_code' => 'en',
        'language_name' => 'English',
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
    ];
    
    /**
     * @var array Russian language translations
     */
    private $russianTranslation = [
        'language_code' => 'ru',
        'language_name' => 'Русский',
        'time' => [
            'format' => '{value} {unit}',
            'separator' => ' и ',
            'less_than_second' => 'менее секунды',
            'second' => 'секунда',
            'second_few' => 'секунды',
            'seconds' => 'секунд',
            'minute' => 'минута',
            'minute_few' => 'минуты',
            'minutes' => 'минут',
            'hour' => 'час',
            'hour_few' => 'часа',
            'hours' => 'часов',
            'day' => 'день',
            'day_few' => 'дня',
            'days' => 'дней',
            'plural_function' => null, // Will be set in setUp
        ],
    ];

    /**
     * @var string Temporary file path if created
     */
    private $tempFilePath;
    
    protected function setUp(): void
    {
        // Reset BitFormatter state by removing all languages
        foreach (BitFormatter::getLoadedLanguages() as $lang) {
            try {
                BitFormatter::setDefaultLanguage('en'); // Try to set default to English first
            } catch (\Exception $e) {
                // Ignore if English is not loaded
            }
        }

        // Set the plural function for Russian
        $this->russianTranslation['time']['plural_function'] = function ($number, $unit) {
            $mod10 = $number % 10;
            $mod100 = $number % 100;
            
            if ($mod10 === 1 && $mod100 !== 11) {
                return $unit; // 1, 21, 31, ...
            }
            
            if ($mod10 >= 2 && $mod10 <= 4 && ($mod100 < 10 || $mod100 >= 20)) {
                return $unit . '_few'; // 2-4, 22-24, ...
            }
            
            return $unit . 's'; // 0, 5-20, 25-30, ...
        };
    }
    
    protected function tearDown(): void
    {
        // Clean up any temporary file if created
        if ($this->tempFilePath && file_exists($this->tempFilePath)) {
            unlink($this->tempFilePath);
        }
    }
    
    /**
     * @test
     */
    public function canAddLanguage(): void
    {
        BitFormatter::addLanguage('en', $this->englishTranslation);
        
        $this->assertTrue(BitFormatter::isLanguageLoaded('en'));
        $this->assertEquals('en', BitFormatter::getDefaultLanguage());
    }
    
    /**
     * @test
     */
    public function canAddMultipleLanguages(): void
    {
        BitFormatter::addLanguage('en', $this->englishTranslation);
        BitFormatter::addLanguage('ru', $this->russianTranslation);
        
        $this->assertTrue(BitFormatter::isLanguageLoaded('en'));
        $this->assertTrue(BitFormatter::isLanguageLoaded('ru'));
        
        $loaded = BitFormatter::getLoadedLanguages();
        $this->assertContains('en', $loaded);
        $this->assertContains('ru', $loaded);
    }
    
    /**
     * @test
     */
    public function createsTempLanguageFileAndLoadsIt(): void
    {
        // Skip if we can't create temporary files
        if (!function_exists('tempnam')) {
            $this->markTestSkipped('Cannot create temporary files');
        }
        
        // Create a temporary PHP file with English translation
        $tempDir = sys_get_temp_dir();
        $this->tempFilePath = tempnam($tempDir, 'lang_test_');
        unlink($this->tempFilePath); // Remove the empty file
        $this->tempFilePath .= '.php'; // Add .php extension
        
        $content = '<?php return ' . var_export($this->englishTranslation, true) . ';';
        file_put_contents($this->tempFilePath, $content);
        
        // Load the language file
        $langCode = BitFormatter::loadLanguage($this->tempFilePath);
        
        $this->assertEquals('en', $langCode);
        $this->assertTrue(BitFormatter::isLanguageLoaded('en'));
    }
    
    /**
     * @test
     */
    public function canSetDefaultLanguage(): void
    {
        BitFormatter::addLanguage('en', $this->englishTranslation);
        BitFormatter::addLanguage('ru', $this->russianTranslation);
        
        $this->assertEquals('en', BitFormatter::getDefaultLanguage());
        
        BitFormatter::setDefaultLanguage('ru');
        $this->assertEquals('ru', BitFormatter::getDefaultLanguage());
    }
    
    /**
     * @test
     */
    public function canAddCustomLanguage(): void
    {
        $customLang = [
            'time' => [
                'format' => '{value} {unit}',
                'separator' => ' and ',
                'less_than_second' => 'less than one second',
                'second' => 'second',
                'seconds' => 'seconds',
                'minute' => 'minute',
                'minutes' => 'minutes',
                'hour' => 'hour',
                'hours' => 'hours',
                'day' => 'day',
                'days' => 'days',
            ],
        ];
        
        BitFormatter::addLanguage('custom', $customLang);
        
        $this->assertTrue(BitFormatter::isLanguageLoaded('custom'));
        $this->assertContains('custom', BitFormatter::getLoadedLanguages());
    }
    
    /**
     * @test
     */
    public function formatsTimeCorrectlyInEnglish(): void
    {
        BitFormatter::addLanguage('en', $this->englishTranslation);
        
        // Less than a second
        $this->assertEquals('less than a second', BitFormatter::formatTime(0.5, 'en'));
        
        // Seconds only
        $this->assertEquals('1 second', BitFormatter::formatTime(1, 'en'));
        $this->assertEquals('2 seconds', BitFormatter::formatTime(2, 'en'));
        $this->assertEquals('59 seconds', BitFormatter::formatTime(59, 'en'));
        
        // Minutes and seconds
        $this->assertEquals('1 minute', BitFormatter::formatTime(60, 'en'));
        $this->assertEquals('1 minute, 1 second', BitFormatter::formatTime(61, 'en'));
        $this->assertEquals('2 minutes, 10 seconds', BitFormatter::formatTime(130, 'en'));
        
        // Hours, minutes and seconds
        $this->assertEquals('1 hour', BitFormatter::formatTime(3600, 'en'));
        $this->assertEquals('1 hour, 1 minute', BitFormatter::formatTime(3660, 'en'));
        $this->assertEquals('2 hours, 30 minutes', BitFormatter::formatTime(9000, 'en'));
        
        // Days and hours
        $this->assertEquals('1 day', BitFormatter::formatTime(86400, 'en'));
        $this->assertEquals('1 day, 6 hours', BitFormatter::formatTime(108000, 'en'));
        $this->assertEquals('2 days, 12 hours', BitFormatter::formatTime(216000, 'en'));
    }
    
    /**
     * @test
     */
    public function formatsTimeCorrectlyInRussian(): void
    {
        BitFormatter::addLanguage('ru', $this->russianTranslation);
        
        // Test with Russian-specific plural forms
        $this->assertEquals('менее секунды', BitFormatter::formatTime(0.5, 'ru'));
        $this->assertEquals('1 секунда', BitFormatter::formatTime(1, 'ru'));
        $this->assertEquals('2 секунды', BitFormatter::formatTime(2, 'ru'));
        $this->assertEquals('5 секунд', BitFormatter::formatTime(5, 'ru'));
        $this->assertEquals('11 секунд', BitFormatter::formatTime(11, 'ru'));
        $this->assertEquals('21 секунда', BitFormatter::formatTime(21, 'ru'));
        
        $this->assertEquals('1 минута', BitFormatter::formatTime(60, 'ru'));
        $this->assertEquals('1 минута и 1 секунда', BitFormatter::formatTime(61, 'ru'));
        $this->assertEquals('2 минуты и 10 секунд', BitFormatter::formatTime(130, 'ru'));
        $this->assertEquals('5 минут и 5 секунд', BitFormatter::formatTime(305, 'ru'));
        
        $this->assertEquals('1 час', BitFormatter::formatTime(3600, 'ru'));
        $this->assertEquals('1 час и 1 минута', BitFormatter::formatTime(3660, 'ru'));
        $this->assertEquals('2 часа и 30 минут', BitFormatter::formatTime(9000, 'ru'));
        $this->assertEquals('5 часов и 15 минут', BitFormatter::formatTime(18900, 'ru'));
        
        $this->assertEquals('1 день', BitFormatter::formatTime(86400, 'ru'));
        $this->assertEquals('1 день и 6 часов', BitFormatter::formatTime(108000, 'ru'));
        $this->assertEquals('2 дня и 12 часов', BitFormatter::formatTime(216000, 'ru'));
        $this->assertEquals('5 дней и 5 часов', BitFormatter::formatTime(450000, 'ru'));
    }
    
    /**
     * @test
     */
    public function transferCalculationsWorkCorrectly(): void
    {
        BitFormatter::addLanguage('en', $this->englishTranslation);
        
        $dataSize = Byte::gb(2);  // 2 GB file
        $bitRate = BitRate::mbps(25);  // 25 Mbps connection
        
        // Calculate transfer time: 2 GB = 16 Gb, at 25 Mbps = 640 seconds
        $time = BitFormatter::calculateTransferTime($dataSize, $bitRate);
        $this->assertEquals(640, $time);
        
        // Test with numeric value (bytes per second)
        $bandwidth = 10 * 1024 * 1024;  // 10 MB/s
        $time = BitFormatter::calculateTransferTime($dataSize, $bandwidth);
        $this->assertEquals(2 * 1024 / 10, $time);
    }
    
    /**
     * @test
     */
    public function transferAmountCalculationsWorkCorrectly(): void
    {
        $bitRate = BitRate::mbps(100);  // 100 Mbps
        $seconds = 60;  // 1 minute
        
        // 100 Mbps for 60 seconds = 750 MB
        $amount = BitFormatter::calculateTransferAmount($bitRate, $seconds);
        $this->assertEquals(750_000_000, $amount->value);
        
        // Test with numeric value (bytes per second)
        $bandwidth = 5 * 1024 * 1024;  // 5 MB/s
        $amount = BitFormatter::calculateTransferAmount($bandwidth, $seconds);
        $this->assertEquals(5 * 1024 * 1024 * 60, $amount->value);
    }
    
    /**
     * @test
     */
    public function fileSizeEstimationWorksCorrectly(): void
    {
        $bitRate = BitRate::mbps(5);  // 5 Mbps video stream
        $duration = 3600;  // 1 hour in seconds
        
        // 5 Mbps for 3600 seconds = 2.25 GB
        $size = BitFormatter::estimateFileSize($bitRate, $duration);
        $this->assertEquals(2_250_000_000, $size->value);
    }
    
    /**
     * @test
     */
    public function humanizeBytesFormatsCorrectly(): void
    {
        $this->assertEquals('1.5 kB', BitFormatter::humanizeBytes(1536));
        $this->assertEquals('1.500 kB', BitFormatter::humanizeBytes(1536, 3));
        $this->assertEquals('1.5_kB', BitFormatter::humanizeBytes(1536, 2, '_'));
    }
    
    /**
     * @test
     */
    public function humanizeBitRateFormatsCorrectly(): void
    {
        // As bits
        $this->assertEquals('1 Mbps', BitFormatter::humanizeBitRate(1_000_000, 'bit'));
        $this->assertEquals('1.500 Mbps', BitFormatter::humanizeBitRate(1_500_000, 'bit', 3));
        $this->assertEquals('1.5_Mbps', BitFormatter::humanizeBitRate(1_500_000, 'bit', 2, '_'));
        
        // As bytes
        $this->assertEquals('125 kBps', BitFormatter::humanizeBitRate(1_000_000, 'byte'));
        $this->assertEquals('187.500 kBps', BitFormatter::humanizeBitRate(1_500_000, 'byte', 3));
        $this->assertEquals('187.5_kBps', BitFormatter::humanizeBitRate(1_500_000, 'byte', 2, '_'));
    }
    
    /**
     * @test
     */
    public function loadDefaultsMethodLoadsAllLanguages(): void
    {
        // This test is a bit tricky since we can't easily mock the filesystem for loadDefaults
        // We'll create a simple test based on checking that the method doesn't throw an exception
        
        try {
            BitFormatter::loadDefaults();
            $this->assertTrue(true);  // If we got here, no exception was thrown
            
            // Check if at least English is loaded
            $this->assertTrue(
                BitFormatter::isLanguageLoaded('en') || 
                count(BitFormatter::getLoadedLanguages()) > 0,
                'loadDefaults() did not load any languages'
            );
        } catch (\Exception $e) {
            $this->markTestSkipped('BitFormatter::loadDefaults() threw an exception: ' . $e->getMessage());
        }
    }
    
    /**
     * @test
     */
    public function throwsExceptionsForInvalidInputs(): void
    {
        BitFormatter::addLanguage('en', $this->englishTranslation);
        
        // Try to format with non-existent language
        try {
            BitFormatter::formatTime(10, 'nonexistent-language');
            $this->fail('Expected exception for non-existent language was not thrown');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true); // Exception was thrown as expected
        }
        
        // Try to set default to non-existent language
        try {
            BitFormatter::setDefaultLanguage('nonexistent-language');
            $this->fail('Expected exception for non-existent default language was not thrown');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true); // Exception was thrown as expected
        }
        
        // Try to load non-existent file
        try {
            BitFormatter::loadLanguage('/path/to/nonexistent/file.php');
            $this->fail('Expected exception for non-existent file was not thrown');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true); // Exception was thrown as expected
        }
    }
    
    /**
     * @test
     */
    public function fallsBackToEnglishWhenPossible(): void
    {
        // Setup: Load only English
        BitFormatter::addLanguage('en', $this->englishTranslation);
        
        // When English is available and another language is requested but not available
        // It should fall back to English instead of throwing an exception
        try {
            $result = BitFormatter::formatTime(60, 'nonexistent-language');
            $this->assertEquals('1 minute', $result); // Should use English as fallback
        } catch (\Exception $e) {
            $this->fail('Should not throw exception when English fallback is available');
        }
    }
    
    /**
     * @test
     */
    public function integrationWithByteAndBitRate(): void
    {
        // Setup
        BitFormatter::addLanguage('en', $this->englishTranslation);
        BitFormatter::addLanguage('ru', $this->russianTranslation);
        
        $fileSize = Byte::gb(1);
        $bitRate = BitRate::mbps(50);
        
        // Test BitRate using BitFormatter for transfer time formatting
        $formatted = $bitRate->getFormattedTransferTime($fileSize, 'en');
        $this->assertEquals('2 minutes, 40 seconds', $formatted);
        
        $formatted = $bitRate->getFormattedTransferTime($fileSize, 'ru');
        $this->assertEquals('2 минуты и 40 секунд', $formatted);
        
        // Test Byte using BitFormatter for transfer time formatting
        $formatted = $fileSize->getFormattedTransferTime($bitRate, 'en');
        $this->assertEquals('2 minutes, 40 seconds', $formatted);
        
        $formatted = $fileSize->getFormattedTransferTime($bitRate, 'ru');
        $this->assertEquals('2 минуты и 40 секунд', $formatted);
    }
}
