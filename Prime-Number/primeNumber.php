<?php

function primeNum($input)
{
    $x = 2;

    $emp_string = "";

    if ($input < 2) {
        $non_prime = " ";
        return $non_prime;
    }

    while ($x < $input) {

        $count = 0;

        for ($i = 1; $i <= $input; $i++) {
            if ($x % $i == 0)
                $count++;
        }

        if ($count < 3) {
            $emp_string .= $x . ",";
        }

        $x++;
    }

    return $emp_string;
}


function test()
{

    if ("2,3,5,7," == primeNum(10)) echo "Test passed<br>";

    if (" " == primeNum(1)) echo "Test passed<br>";

    if ("2,3,5,7,11,13,17,19,23,29,31,37,41,43,47,53,59,61,67,71,73,79,83,89,97," == primeNum(100))

        echo "Test passed<br>";
}

test();
