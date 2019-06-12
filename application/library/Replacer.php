<?php


class Replacer
{
    private static $specialCharList = [
        "'",
        " "
    ];

    public static function SpecialChars(string $string) : string
    {
        return addslashes($string);
    }
}