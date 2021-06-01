<?php

echo <<<_END
	<html>
		<head>
			<title> Text File Upload </title>
		</head>
			<form method = 'post' action ='greatest.php' enctype='multipart/form-data'>
					Select only a Txt File: 
					<p><input type='file' name='filename' size='10'></p>
					<p><input type='submit' name = 'Upload' value='Upload'></p>
			</form>
	_END;

if ($_FILES) {

	$name = htmlentities($_FILES['filename']['name']);
	$tempname = $_FILES['filename']['tmp_name'];
	$type = htmlentities($_FILES['filename']['type']);
	$size = htmlentities($_FILES['filename']['size']);
	//create my own upload fie folder for preventing malicious attack 
	// $filenameWithDirectory = "uploaded-file/" . $name;

	// exclusive txt extension and size check
	if ($type == 'text/plain') {
		if ($size == 0 || $size > 100000) {
			echo "The file is either empty or too large";
			die();
		} else {
			// move_uploaded_file($tempname, $filenameWithDirectory);
			echo "<em>Upload success</em><br><br>";
		}
	} else {
		echo "Sorry, only TXT file is allowed.";
	}

	//extract numbers from the file
	$fh = fopen("$filenameWithDirectory", "r");
	$data = "";
	while (!feof($fh)) {
		//ignore the white spaces and new line
		$data .= trim(fgetc($fh));
	}
	fclose($fh);

	//find the largest product
	$array = make_grid($data);
	$largest = 0;

	$row_max = row_product($array);
	$col_max = col_product($array);
	$dia_max = diagonal($array);
	$diag_ltr = supdiag_ltr($array);
	$diag_rtl = supdiag_rtl($array);
	$max_of_diag = max_diag($dia_max, $diag_ltr, $diag_rtl);
	$largest = cal_max($row_max, $col_max, $max_of_diag);

	echo "Row max: " . "$row_max<br>";
	echo "Col max: " . "$col_max<br>";
	echo "Normal diaganol: " . "$dia_max<br>";
	echo "Diagonal left to right: " . "$diag_ltr<br>";
	echo "Diagonal right to left:" . "$diag_rtl<br>";
	echo "Max of all diagonal: " . "$max_of_diag<br><br>";
	echo "The greatest product of four adjacent numbers in all the four possible directions is: " . "$largest" . "<br><br>";

	test($largest, $row_max, $col_max, $dia_max, $diag_rtl);
}

echo "</body></html>";

function cal_max($max_r, $max_c, $max_dia)
{
	$largest = 0;
	//compare product of row, col and diagonal
	if ($max_r > $max_c) {
		if ($max_r > $max_dia)
			$largest = $max_r;
		else
			$largest = $max_dia;
	} else {
		if ($max_c > $max_dia)
			$largest = $max_c;
		else
			$largest = $max_dia;
	}

	return $largest;
}
function make_grid($data)
{
	$array_grid = array();
	for ($row = 0; $row < 20; $row++) {
		for ($col = 0; $col < 20; $col++) {
			$index = ($row * 20) + $col;
			$array_grid[$row][$col] = $data[$index];
		}
	}

	return $array_grid;
}

function row_product($grid)
{
	$max = 0;
	$result = 0;
	$last_three = 0;
	for ($row = 0; $row < 19; $row++) {
		for ($col = 0; $col < 17; $col++) {
			$tmp = $result;
			$result = $grid[$row][$col] * $grid[$row][$col + 1] * $grid[$row][$col + 2] * $grid[$row][$col + 3];
			if ($result < $tmp)
				$result = $tmp;

			//product for last three numbers in a row 
			$p1 = $grid[$row][17] * $grid[$row][18] * $grid[$row][19] * $grid[$row + 1][0];
			$p2 = $grid[$row][18] * $grid[$row][19] * $grid[$row + 1][0] * $grid[$row + 1][1];
			$p3 = $grid[$row][19] * $grid[$row + 1][0] * $grid[$row + 1][1] * $grid[$row + 1][2];

			if ($p1 > $p2) {
				if ($p1 > $p3)
					$last_three = $p1;
				else
					$last_three = $p3;
			} else {
				if ($p2 > $p3)
					$last_three = $p2;
				else
					$last_three = $p3;
			}

			if ($result > $last_three)
				$max = $result;
			else
				$max = $last_three;
		}
	}

	return $max;
}

function col_product($grid)
{
	$max = 0;
	$result = 0;
	$last_three = 0;
	for ($col = 0; $col < 19; $col++) {
		for ($row = 0; $row < 17; $row++) {
			$tmp = $result;
			$result = $grid[$row][$col] * $grid[$row + 1][$col] * $grid[$row + 2][$col] * $grid[$row + 3][$col];
			if ($result < $tmp)
				$result = $tmp;

			//product for last three numbers in a column 
			$p1 = $grid[17][$col] * $grid[18][$col] * $grid[19][$col] * $grid[0][$col + 1];
			$p2 = $grid[18][$col] * $grid[19][$col] * $grid[0][$col + 1] * $grid[1][$col + 1];
			$p3 = $grid[19][$col] * $grid[0][$col + 1] * $grid[1][$col + 1] * $grid[2][$col + 1];

			if ($p1 > $p2) {
				if ($p1 > $p3)
					$last_three = $p1;
				else
					$last_three = $p3;
			} else {
				if ($p2 > $p3)
					$last_three = $p2;
				else
					$last_three = $p3;
			}

			if ($result > $last_three)
				$max = $result;
			else
				$max = $last_three;
		}
	}

	return $max;
}

function diagonal($grid)
{

	$result = 0;
	for ($row = 0; $row < 17; $row++) {
		for ($col = 0; $col < 17; $col++) {
			//product of 4 diagonals from left to right
			if ((($row + 3) < 20) && (($col + 3) < 20)) {
				$tmp = $result;
				$result = $grid[$row][$col] * $grid[$row + 1][$col + 1] * $grid[$row + 2][$col + 2] * $grid[$row + 3][$col + 3];
				if ($result < $tmp)
					$result = $tmp;
			}

			//product of 4 diagonals from right to left
			if ((($row + 3) < 20) && (($col - 3) >= 0) && $col > 3) {
				$tmp = $result;
				$result = $grid[$row][$col] * $grid[$row + 1][$col - 1] * $grid[$row + 2][$col - 2] * $grid[$row + 3][$col - 3];
				if ($result < $tmp)
					$result = $tmp;
			}
		}
	}

	return $result;
}

function supdiag_ltr($grid)
{
	$result = 0;
	//product for last three numbers in a diagonal. Supplement diagonal when slicing horizontally.
	for ($row = 0; $row < 17; $row++) {
		for ($col = 17; $col < 20; $col++) {

			$tmp = $result;
			if ($col == 17) {
				$result = $grid[$row][$col] * $grid[$row + 1][$col + 1] * $grid[$row + 2][$col + 2] * $grid[$row + 3][0];
				if ($result < $tmp)
					$result = $tmp;
			} elseif ($col == 18) {
				$result = $grid[$row][$col] * $grid[$row + 1][$col + 1] * $grid[$row + 2][0] * $grid[$row + 3][1];
				if ($result < $tmp)
					$result = $tmp;
			} elseif ($col == 19) {
				$result = $grid[$row][$col] * $grid[$row + 1][0] * $grid[$row + 2][1] * $grid[$row + 3][2];
				if ($result < $tmp)
					$result = $tmp;
			}
		}

		return $result;
	}
}


function supdiag_rtl($grid)
{

	$result = 0;

	//product for last three numbers in a diagonal. Only consider the condition of supplementing diagonal when slicing vertically.
	for ($row = 0; $row < 17; $row++) {
		for ($col = 17; $col < 20; $col++) {
			$tmp = $result;

			if ($col == 17) {
				$result = $grid[$row + 3][$col] * $grid[$row + 2][$col + 1] * $grid[$row + 1][$col + 2] * $grid[$row][0];
				if ($result < $tmp)
					$result = $tmp;
			} elseif ($col == 18) {
				$result = $grid[$row + 3][$col] * $grid[$row + 2][$col + 1] * $grid[$row + 1][0] * $grid[$row][1];
				if ($result < $tmp)
					$result = $tmp;
			} elseif ($col == 19) {
				$p3 = $grid[$row + 3][$col] * $grid[$row + 2][0] * $grid[$row + 1][1] * $grid[$row][2];
				if ($result < $tmp)
					$result = $tmp;
			}
		}
	}

	return $result;
}

function max_diag($normal, $sup_ltr, $sup_rtl)
{
	//$normal,$sup_ltr,$sup_rtl
	//biggest product among normal diagonal, supplement diagnoal in row, and supplement diagnoal in colunm.
	$max = 0;
	if ($normal > $sup_ltr) {
		if ($normal > $sup_rtl)
			$max = $normal;
		else
			$max = $sup_rtl;
	} else {
		if ($sup_ltr > $sup_rtl)
			$max = $sup_ltr;
		else
			$max = $sup_rtl;
	}

	return $max;
}

function test($largest, $row_max, $col_max, $dia_max, $dia_sup_max)
{
	/*  71636269561882670428
	    85861560789112949495
	    65727333001053367881
		52584907711670556013
		53697817977846174064
		83972241375657056057
		82166370484403199890
		96983520312774506326
		12540698747158523863
		66896648950445244523
		05886116467109405077
		16427171479924442928
		17866458359124566529
		24219022671055626321
		07198403850962455444
		84580156166097919133
		62229893423380308135
		73167176531330624919
		30358907296290491560
		70172427121883998797
*/

	//Since the largest product appears in a row, we combine the row test and largest test together.
	//testcase 1: is the largest product of 4 adjacent numbers in all direction equals to the product of "9,9,8,9" spotted in row #6, col #15?
	$cal_max = 9 * 9 * 8 * 9;
	if ($largest == $cal_max && $row_max == $cal_max)
		echo "Test passed, the largest product " . $largest . " of 4 adjacent numbers in all directions match.<br>";
	else
		echo "Test failed, the largest product of 4 adjacent numbers in all directions didn't match.<br>";

	//testcase 2: is the largest product of 4 adjacent numbers in a column equals to the product of "9,7,6,8" spotted in row #3, col #4?
	$cal_col = 9 * 7 * 6 * 8;
	if ($col_max == $cal_col)
		echo "Test passed, the largest product " . $col_max . " of 4 adjacent numbers in a column match.<br>";
	else
		echo "Test failed, the largest product of 4 adjacent numbers in a column didn't match.<br>";

	//testcase 3: is the largest product of 4 adjacent numbers in a diagonal equals to the product of "9,9,7,8"(from right to left) spotted in row #11, col #8?
	$cal_dia = 9 * 9 * 7 * 8;
	if ($dia_max == $cal_dia)
		echo "Test passed, the largest product " . $dia_max . " of 4 adjacent numbers in a diagonal(without supplement) match.<br>";
	else
		echo "Test failed, the largest product of 4 adjacent numbers in a diagonal(without supplement) didn't match.<br>";

	//testcase 4: is the largest product of 4 adjacent numbers in a supplemental diagonal equals to the product of "6,9,6,7"(from right to left) spotted in row #17, col #18, with supplement "6" grid[16][0].
	$cal_dia_sup = 6 * 9 * 6 * 7;
	if ($dia_sup_max == $cal_dia_sup)
		echo "Test passed, the largest product " . $dia_sup_max . " of 4 adjacent numbers in a supplemental diagonal match.<br>";
	else
		echo "Test failed, the largest product of 4 adjacent numbers in a supplemental diagonal didn't match.<br>";
}
