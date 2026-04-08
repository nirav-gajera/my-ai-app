<?php

namespace App\Helpers;

class TextHelper
{
    public static function chunk($text, $size = 500)
    {
        return str_split($text, $size);
    }
}