<?php
/**
 * Chinese (Simplified) translations
 */
return [
    'language_code' => 'zh',
    'language_name' => '中文',
    'time' => [
        'format' => '{value}{unit}',  // No space in Chinese
        'separator' => '',
        'less_than_second' => '不到一秒',
        'second' => '秒',
        'seconds' => '秒',
        'minute' => '分钟',
        'minutes' => '分钟',
        'hour' => '小时',
        'hours' => '小时',
        'day' => '天',
        'days' => '天',
        // In Chinese, measure words don't change with plural
        'plural_function' => function ($number, $unit) {
            return $unit . 's';
        },
    ],
];
