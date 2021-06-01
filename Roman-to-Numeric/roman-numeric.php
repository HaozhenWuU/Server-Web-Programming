<?php

function romanToArabic($roman)
{
    $roman = strtoupper($roman);
    $arabic = 0;

    //array of roman symbol and the matching value 
    $symbol = array(
        "I" => 1,
        "V" => 5,
        "X" => 10,
        "L" => 50,
        "C" => 100,
        "D" => 500,
        "M" => 1000,
        // Subtractive notation
        "IV" => 4,
        "IX" => 9,
        "XL" => 40,
        "XC" => 90,
        "CD" => 400,
        "CM" => 900
    );

    $pointer = 0;
    while ($pointer < strlen($roman)) {

        $two_char = substr($roman, $pointer, 2);

        /*  if the first two characters match a key in the array(subtractive notation), add the value to arabic, pointer moves on 2 position. If not, add the first character value to arabic, pointers moves on 1 position and check next 2 characters again*/
        if (array_key_exists($two_char, $symbol)) {
            $arabic += $symbol[$two_char];
            $pointer += 2;
        } else {
            $one_char = substr($roman, $pointer, 1);
            if (array_key_exists($one_char, $symbol)) {
                $arabic += $symbol[$one_char];
                $pointer += 1;
            }
        }
    }
    return $arabic;
}

function test($roman)
{
    if (empty($roman) or !is_string($roman))
        echo "Invalid input <br>";
    else {
        $result = romanToArabic($roman);
        echo "The Arabic numeral of " . $roman . " is " . $result . "<br>";
    }
}

test(123);
test("");
test("xxxix");
test("VI");
test("IV");
test("MCMXC");
test("IX");
test("CD");
test("CCXLVI");
