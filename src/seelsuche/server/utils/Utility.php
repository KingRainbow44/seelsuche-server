<?php

namespace seelsuche\server\utils;

final class Utility
{
    public static function validateArray(array $array, $compareTo): bool{
        $valid = true;
        foreach($array as $item)
            $valid = is_a($array, $compareTo, true);
        return $valid;
    }
}