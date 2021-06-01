<?php

echo <<<_END
    <form method = 'post' action ='MySql.php' enctype='multipart/form-data'>
    Upload only a Txt File Here: 
    <p><input type='file' name='file' size='10'></p><br>
    Enter the name for your file:
    <p><input type='text' name='newname'></p>
    <p><input type='submit' name = 'upload' value='Upload'></p>
    </form>
 
_END;

//make connection to the database;
$connection = getConnection();

//handle the input file name
if (isset($_POST['upload'])) {

    $newname = htmlentities($_POST['newname']);
}

//handling the uploaded file
if ($_FILES) {

    $name = htmlentities($_FILES['file']['name']);
    $tempname = $_FILES['file']['tmp_name'];
    $type = htmlentities($_FILES['file']['type']);
    $size = htmlentities($_FILES['file']['size']);
    $filenameWithDirectory = "uploaded-file/" . $newname;

    // exclusive txt extension and size check
    if ($type == 'text/plain') {
        if ($size == 0 || $size > 100000) {
            echo "The file is either empty or too large";
            die();
        } else {
            move_uploaded_file($tempname, $filenameWithDirectory);
            echo "<em>Upload success</em><br>";
            toMysql($filenameWithDirectory, $newname, $connection);
        }
    } else {
        echo "Sorry, only text file is allowed.";
    }
}

echo <<<_END
        <table>
        <tr><td><strong>Id</strong></td>
        <td><strong>Name</strong></td>
        <td><strong>Content</strong></td></tr>
_END;

//display the query result 
$query_display = "SELECT * from file";
$result = $connection->query($query_display);
if (!$result) {
    echo "failed to display the table of the database";
}
$rows = $result->num_rows;

for ($i = 0; $i < $rows; $i++) {
    $result->data_seek($i);
    $row = $result->fetch_array(MYSQLI_NUM);
    echo "<tr>";

    for ($k = 0; $k < 3; ++$k) echo "<td>$row[$k]</td>";
    echo "</tr>";
}

echo "</table>";


function toMysql($path, $name, $connection)
{
    //extract conmtents from the file
    $fh = fopen("$path", "r");
    $data = "";
    while (!feof($fh)) {
        //ignore the white spaces and new line
        $data .= trim(fgetc($fh));
    }
    fclose($fh);

    //start the sql query 
    $query_insert = "INSERT INTO file(name,content) VALUES ('$name','$data')";
    $result = $connection->query($query_insert);
    if ($result) {
        echo "<em>Insert successfully</em><br><br>";
    } else {
        echo "failed to access database.";
    }
}

function getConnection()
{

    require_once 'login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_error) die(mysql_error());
    return $conn;
}

function mysql_error()
{
    echo <<<_END

    We are sorry, but it was not possible to complete the requested task.
    There could be errors on either connecting to the database or querying the database. 
    _END;
}
