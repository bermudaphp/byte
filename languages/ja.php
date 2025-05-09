<?php
/**
 * Japanese translations
 */
return [
    'language_code' => 'ja',
    'language_name' => '日本語',
    'time' => [
        'format' => '{value}{unit}',  // No space in Japanese
        'separator' => '',
        'less_than_second' => '1秒未満',
        'second' => '秒',
        'seconds' => '秒',
        'minute' => '分',
        'minutes' => '分',
        'hour' => '時間',
        'hours' => '時間',
        'day' => '日',
        'days' => '日',
        // In Japanese, there are no plural forms
        'plural_function' => function ($number, $unit) {
            return $unit . 's';
        },
    ],
];
