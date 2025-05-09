<?php

namespace Bermuda\Stdlib;

/**
 * Class BitFormatter
 *
 * Static helper class for formatting time, file sizes, and bit rates with multi-language support.
 */
final class BitFormatter
{
    /**
     * Some common language codes for convenience
     */
    public const string LANG_EN = 'en';
    public const string LANG_RU = 'ru';
    public const string LANG_FR = 'fr';
    public const string LANG_DE = 'de';
    public const string LANG_ES = 'es';
    public const string LANG_IT = 'it';
    public const string LANG_PT = 'pt';
    public const string LANG_ZH = 'zh';
    public const string LANG_JA = 'ja';
    public const string LANG_AR = 'ar';

    /**
     * Default language to use for formatting.
     *
     * @var string
     */
    private static string $defaultLanguage = self::LANG_EN;

    /**
     * Localization strings for different languages.
     *
     * @var array<string, array<string, array<string, string>>>
     */
    private static array $localization = [];

    /**
     * Sets the default language for formatting.
     *
     * @param string $language Language code ('en', 'ru', etc.)
     * @return void
     * @throws \InvalidArgumentException If the language is not loaded
     */
    public static function setDefaultLanguage(string $language): void
    {
        if (!isset(self::$localization[$language])) {
            throw new \InvalidArgumentException("Language '$language' is not loaded. Use loadLanguage() first.");
        }

        self::$defaultLanguage = $language;
    }

    /**
     * Gets the current default language.
     *
     * @return string The default language code
     */
    public static function getDefaultLanguage(): string
    {
        return self::$defaultLanguage;
    }

    /**
     * Checks if a language is loaded.
     *
     * @param string $languageCode The language code to check
     * @return bool True if the language is loaded
     */
    public static function isLanguageLoaded(string $languageCode): bool
    {
        return isset(self::$localization[$languageCode]);
    }

    /**
     * Gets all loaded languages.
     *
     * @return array<string> Array of loaded language codes
     */
    public static function getLoadedLanguages(): array
    {
        return array_keys(self::$localization);
    }

    /**
     * Adds a new language to the localization array.
     *
     * @param string $languageCode The language code (e.g., 'fr', 'de')
     * @param array $translationData The translation data for the language
     * @return void
     */
    public static function addLanguage(string $languageCode, array $translationData): void
    {
        self::$localization[$languageCode] = $translationData;
    }

    /**
     * Loads a language from a PHP file.
     *
     * @param string $filePath Absolute or relative path to the PHP file with translations
     * @param string|null $languageCode Optional language code. If null, the code is extracted from the file
     * @return string The language code of the loaded file
     * @throws \InvalidArgumentException If the file doesn't exist or doesn't return a valid translation array
     */
    public static function loadLanguage(string $filePath, ?string $languageCode = null): string
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("Translation file does not exist: $filePath");
        }

        $translation = include $filePath;

        if (!is_array($translation)) {
            throw new \InvalidArgumentException("Translation file must return an array: $filePath");
        }

        if ($languageCode === null) {
            // Try to extract language code from the file
            if (isset($translation['language_code'])) {
                $languageCode = $translation['language_code'];
                unset($translation['language_code']);
            } else {
                // Try to extract from filename
                $filename = basename($filePath, '.php');
                if (preg_match('/^[a-z]{2}(?:[-_][a-zA-Z]{2})?$/', $filename)) {
                    $languageCode = $filename;
                } else {
                    throw new \InvalidArgumentException(
                        "Cannot determine language code from file. Please specify it explicitly."
                    );
                }
            }
        }

        self::$localization[$languageCode] = $translation;

        // If this is the first language loaded, set it as default
        if (count(self::$localization) === 1) {
            self::$defaultLanguage = $languageCode;
        }

        return $languageCode;
    }

    /**
     * Loads the default language files shipped with the library.
     *
     * This method automatically loads all language translations from the package's
     * 'languages' directory. It's a convenient way to initialize all available
     * translations without having to manually specify each language file.
     *
     * Usage:
     * ```
     * BitFormatter::loadDefaults();
     * // Now you can use any of the pre-packaged languages
     * echo BitFormatter::formatTime(3665, 'ru'); // "1 час и 1 минута"
     * ```
     *
     * @return void
     * @throws \InvalidArgumentException If the default languages directory cannot be accessed
     *                                  or contains invalid translation files
     */
    public static function loadDefaults(): void
    {
        self::loadLanguagesFromDirectory(dirname(__DIR__) . '/languages');
    }

    /**
     * Loads multiple language files from a directory.
     *
     * @param string $directory Directory containing translation files
     * @param string $pattern Optional glob pattern to match specific files. Default: '*.php'
     * @return array<string> Array of loaded language codes
     */
    public static function loadLanguagesFromDirectory(string $directory, string $pattern = '*.php'): array
    {
        $loadedLanguages = [];
        $directory = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR;

        foreach (glob($directory . $pattern) as $filePath) {
            try {
                $languageCode = self::loadLanguage($filePath);
                $loadedLanguages[] = $languageCode;
            } catch (\Exception $e) {
                // Skip files that can't be loaded
                continue;
            }
        }

        return $loadedLanguages;
    }

    /**
     * Formats a time value in seconds to a human-readable string.
     *
     * @param float $seconds The time in seconds
     * @param string|null $language Language to use for formatting (null uses default)
     * @return string A formatted time string
     * @throws \InvalidArgumentException If the language is not loaded
     */
    public static function formatTime(float $seconds, ?string $language = null): string
    {
        $lang = $language ?? self::$defaultLanguage;

        if (!isset(self::$localization[$lang])) {
            // Try to fall back to English if available
            if ($lang !== self::LANG_EN && isset(self::$localization[self::LANG_EN])) {
                $lang = self::LANG_EN;
            } else {
                throw new \InvalidArgumentException("Language '$lang' is not loaded. Use loadLanguage() first.");
            }
        }

        $localization = self::$localization[$lang]['time'];

        if ($seconds < 1) {
            return $localization['less_than_second'];
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = (int) $seconds % 60;

        if ($minutes < 1) {
            $formattedSeconds = round($remainingSeconds);
            return self::formatUnit($formattedSeconds, 'second', $lang);
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = (int) $minutes % 60;

        if ($hours < 1) {
            $result = self::formatUnit($remainingMinutes, 'minute', $lang);
            if ($remainingSeconds > 0) {
                $result .= self::getTimeSeparator($lang) . self::formatUnit(round($remainingSeconds), 'second', $lang);
            }
            return $result;
        }

        $days = floor($hours / 24);
        $remainingHours = (int) $hours % 24;

        if ($days < 1) {
            $result = self::formatUnit($remainingHours, 'hour', $lang);
            if ($remainingMinutes > 0) {
                $result .= self::getTimeSeparator($lang) . self::formatUnit($remainingMinutes, 'minute', $lang);
            }
            return $result;
        }

        $result = self::formatUnit($days, 'day', $lang);
        if ($remainingHours > 0) {
            $result .= self::getTimeSeparator($lang) . self::formatUnit($remainingHours, 'hour', $lang);
        }

        return $result;
    }

    /**
     * Formats a numeric value with the appropriate unit form.
     *
     * @param int $value The numeric value
     * @param string $unit The base unit type ('second', 'minute', etc.)
     * @param string $language The language to use
     * @return string Formatted string with the value and unit
     */
    private static function formatUnit(int $value, string $unit, string $language): string
    {
        $localization = self::$localization[$language]['time'];

        if (isset($localization['plural_function']) && is_callable($localization['plural_function'])) {
            // Use custom plural function if provided
            $pluralForm = call_user_func($localization['plural_function'], $value, $unit);
            return str_replace(
                ['{value}', '{unit}'],
                [$value, $localization[$pluralForm]],
                $localization['format']
            );
        }

        // Default to simple plural forms (like English)
        $unitForm = $value === 1 ? $localization[$unit] : $localization[$unit . 's'];
        return str_replace(
            ['{value}', '{unit}'],
            [$value, $unitForm],
            $localization['format'] ?? '{value} {unit}'
        );
    }

    /**
     * Gets the appropriate separator between time units for the specified language.
     *
     * @param string $language The language code
     * @return string The separator
     */
    private static function getTimeSeparator(string $language): string
    {
        return self::$localization[$language]['time']['separator'] ?? ', ';
    }

    /**
     * Calculates the transfer time in seconds based on file size and transfer rate.
     *
     * @param Byte|int|float|string $dataSize The size of data to transfer
     * @param BitRate|int|float|string $bitRate The transfer rate
     * @return float The time in seconds
     */
    public static function calculateTransferTime(Byte|int|float|string $dataSize, BitRate|int|float|string $bitRate): float
    {
        $bytes = ($dataSize instanceof Byte) ? $dataSize->value : Byte::parse($dataSize);
        $bits = $bytes * 8;

        if ($bitRate instanceof BitRate) {
            if ($bitRate->value <= 0) {
                throw new \InvalidArgumentException("BitRate must be positive");
            }
            return $bits / $bitRate->value;
        }

        // Handle traditional bandwidth input as numeric value (bytes per second)
        $bandwidth = is_numeric($bitRate) ? $bitRate : Byte::parse($bitRate);

        if ($bandwidth <= 0) {
            throw new \InvalidArgumentException("Bandwidth must be positive");
        }

        return $bytes / $bandwidth;
    }

    /**
     * Calculates the amount of data that can be transferred in a given time period.
     *
     * @param BitRate|int|float|string $bitRate The transfer rate
     * @param int|float $seconds The time period in seconds
     * @return Byte A Byte object representing the data size
     */
    public static function calculateTransferAmount(BitRate|int|float|string $bitRate, int|float $seconds): Byte
    {
        if ($bitRate instanceof BitRate) {
            $bits = $bitRate->value * $seconds;
            return new Byte($bits / 8);
        }

        // Handle traditional bandwidth input as numeric value (bytes per second)
        $bandwidth = is_numeric($bitRate) ? $bitRate : Byte::parse($bitRate);
        return new Byte($bandwidth * $seconds);
    }

    /**
     * Estimates the file size for a given bit rate and duration.
     *
     * @param BitRate|int|float|string $bitRate The bit rate
     * @param int|float $seconds The duration in seconds
     * @return Byte A Byte object representing the estimated size
     */
    public static function estimateFileSize(BitRate|int|float|string $bitRate, int|float $seconds): Byte
    {
        return self::calculateTransferAmount($bitRate, $seconds);
    }

    /**
     * Humanizes a byte value to a human-readable string.
     *
     * This is a wrapper for Byte::humanize() for consistency.
     *
     * @param int|float $bytes The number of bytes
     * @param int $precision The number of decimal places
     * @param string $delim The delimiter between number and unit
     * @return string A human-readable string
     */
    public static function humanizeBytes(int|float $bytes, int $precision = 2, string $delim = ' '): string
    {
        return Byte::humanize($bytes, $precision, $delim);
    }

    /**
     * Humanizes a bit rate value to a human-readable string.
     *
     * @param int|float $bitsPerSecond The number of bits per second
     * @param string $type The type of unit to use ('bit' or 'byte')
     * @param int $precision The precision for rounding
     * @param string $delim The delimiter between value and unit
     * @return string A human-readable string
     */
    public static function humanizeBitRate(int|float $bitsPerSecond, string $type = 'bit', int $precision = 2, string $delim = ' '): string
    {
        $rate = new BitRate($bitsPerSecond);
        return $rate->toString($type, $precision, $delim);
    }
}
