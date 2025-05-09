<?php
/**
 * Arabic translations
 */
return [
    'language_code' => 'ar',
    'language_name' => 'العربية',
    'time' => [
        'format' => '{value} {unit}',
        'separator' => ' و ',
        'less_than_second' => 'أقل من ثانية',
        'second' => 'ثانية',
        'seconds' => 'ثوان',
        'seconds_many' => 'ثانية',
        'minute' => 'دقيقة',
        'minutes' => 'دقائق',
        'minutes_many' => 'دقيقة',
        'hour' => 'ساعة',
        'hours' => 'ساعات',
        'hours_many' => 'ساعة',
        'day' => 'يوم',
        'days' => 'أيام',
        'days_many' => 'يوم',
        // Arabic has complex plural rules
        'plural_function' => function ($number, $unit) {
            if ($number == 0) {
                return $unit . 's_many';
            } elseif ($number == 1) {
                return $unit;
            } elseif ($number == 2) {
                return $unit;  // Arabic has dual form, but we simplify here
            } elseif ($number >= 3 && $number <= 10) {
                return $unit . 's';
            } else {
                return $unit . 's_many';
            }
        },
    ],
];
