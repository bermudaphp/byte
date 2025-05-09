<?php
/**
 * Russian translations
 */
return [
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
        // Функция для определения правильной формы существительного в русском языке
        'plural_function' => function ($number, $unit) {
            $mod10 = $number % 10;
            $mod100 = $number % 100;
            
            if ($mod10 === 1 && $mod100 !== 11) {
                return $unit; // 1, 21, 31, ...
            }
            
            if ($mod10 >= 2 && $mod10 <= 4 && ($mod100 < 10 || $mod100 >= 20)) {
                return $unit . '_few'; // 2-4, 22-24, ...
            }
            
            return $unit . 's'; // 0, 5-20, 25-30, ...
        },
    ],
];
