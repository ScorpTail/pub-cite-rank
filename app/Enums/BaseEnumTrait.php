<?php

namespace App\Enums;

trait BaseEnumTrait
{
    public static function getCases($value = false)
    {
        $combinedArray =  array_combine(
            self::getKeys(),
            self::getNames()
        );

        $result = $value
            ? $combinedArray[$value] ?? null
            : $combinedArray;

        return $result;
    }

    #symbols
    public static function getKeys()
    {
        return self::getColumn('value');
    }

    #case name
    public static function getNames(bool $translate = true)
    {
        return $translate
            ? array_map(fn($case) => __($case), self::getColumn('name'))
            : self::getColumn('name');
    }

    public static function getSymbol($value)
    {
        return self::getCases()[$value] ?? null;
    }

    public static function getColumnLikeString($column, $separator = ',')
    {
        return join($separator, self::getColumn($column));
    }

    private static function getColumn($value)
    {
        return array_column(self::cases(), $value);
    }
}
