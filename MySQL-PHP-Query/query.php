<?php

echo <<<_END
    <form method = 'post' action ='query.php' enctype='multipart/form-data'>
    <b>Insert information:</b><br><br>
    <label for="aname">Advisor name: </label>
    <input type="text" id="aname" name="aname"><br><br>
    <label for="sname">Student name:</label>
    <input type="text" id="sname" name="sname"><br><br>
    <label for="id">Student ID code:</label>
    <input type="text" id="id" name="id"><br><br>
    <label for="code">Class code:</label>
    <input type="text" id="code" name="code"><br><br>
    <input type="submit" name = "submit" value="Submit">
    </form>
 
_END;

//make connection to the database;
$connection = getConnection();

//handle the insertion input field with sanitization
if (isset($_POST['submit'])) {

    $adv_name = sanitizeMySQL($connection, $_POST['aname']);
    $stu_name = sanitizeMySQL($connection, $_POST['sname']);
    $stu_id = sanitizeMySQL($connection, $_POST['id']);
    $code = sanitizeMySQL($connection, $_POST['code']);

    //perform the insertion to the database 
    $stmt = $connection->prepare('INSERT INTO student VALUES(?,?,?,?)');
    $stmt->bind_param('ssss', $adv_name, $stu_name, $stu_id, $code);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo "Number of " . $stmt->affected_rows . " rows inserted.<br><br>";
    }
    $stmt->close();
}


echo <<<_END
    <form method = 'post' action ='query.php' enctype='multipart/form-data'>
    <b>Search Advisor</b><br><br>
    <label for="info">Search Advisor: </label>
    <input type="text" id="advi" name="advi"><br><br>
    <input type="submit" name = "search" value="Submit">
    </form>

_END;

//handle the query input with sanitization
if (isset($_POST['search'])) {

    $advisor = sanitizeMySQL($connection, $_POST['advi']);

    //perform the search query to the database 
    $query_search = "SELECT * FROM student WHERE advisor_name = '$advisor'";
    $s_result = $connection->query($query_search);
    if (!$s_result) {
        echo "Can not find the advisor in the database";
    } else {
        echo "<table CELLPADDING=10 border =1>";
        echo "<tr><td><b>Advisor Name</b></td>
        <td><b>Student Name</b></td>
        <td><b>Student ID</b></td>
        <td><b>Class Code</td></b></tr>";
        while ($row = mysqli_fetch_assoc($s_result)) {
            echo "<tr>";
            echo "<td>{$row['advisor_name']}</td>";
            echo "<td>{$row['student_name']}</td>";
            echo "<td>{$row['student_id']}</td>";
            echo "<td>{$row['class_code']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    $s_result->close();
}

$connection->close();

function getConnection()
{

    require_once 'login.php';
    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_error) die(mysql_error());
    return $conn;
}

//Error message in text.
function mysql_error()
{
    echo <<<_END

    We are sorry, but it was not possible to complete the requested task.
    There could be errors on either connecting to the database or querying the database. 
    _END;
}

function sanitizeString($var)
{
    $var = stripslashes($var);
    $var = strip_tags($var);
    $var = htmlentities($var);
    return $var;
}
function sanitizeMySQL($connection, $var)
{
    $var = sanitizeString($var);
    $var = $connection->real_escape_string($var);
    return $var;
}
