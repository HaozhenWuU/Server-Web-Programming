<?php


session_start();
//establish connection
$connection = getConnection();

//check the session
if (isset($_SESSION['uname'])) {
    $username = $_SESSION['uname'];
    echo "Welcome back $username.<br><br>";

    if ($_SESSION['check'] != hash('ripemd128', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']))
        different_user();
} else {
    echo "Please <a href='userlogin.php'>click here</a> to log in.";
}

echo <<<_END

<form method = 'post' action ='userupload.php' enctype='multipart/form-data'>
Upload only a Txt File Here: 
<p><input type='file' name='file' size='10'></p><br>
<p><input type='submit' name = 'upload' value='Upload'></p>
</form><br><br>

_END;


//expire the login session after 10 mins
if (isset($_SESSION['uname'])) {
    if (time() - $_SESSION['time_stamp'] > 600) {
        destroy_session_and_data();
        header("Location: userlogin.php");
    }
} else {
    header("Location: userlogin.php");
}

//handling the uploaded file
if ($_FILES) {

    $name = htmlentities($_FILES['file']['name']);
    $tempname = $_FILES['file']['tmp_name'];
    $type = htmlentities($_FILES['file']['type']);
    $size = htmlentities($_FILES['file']['size']);
    $filenameWithDirectory = "uploaded-file/" . $name;

    // exclusive txt extension and size check
    if ($type == 'text/plain') {
        if ($size == 0 || $size > 100000) {
            echo "The file is either empty or too large";
            die();
        } else {
            move_uploaded_file($tempname, $filenameWithDirectory);
            echo "<em><b>Upload success</b></em><br><br>";
            toMysql($filenameWithDirectory, $username, $name, $connection);
            echo "<br>--------------------------------------";
        }
    } else {
        echo "Sorry, only text file is allowed.";
    }
}


//if no file uploaded, display the default questions set
$path = "uploaded-file\defalut_question.txt";
$default_ques = random_que($path);
echo <<<_END
      <br>Default Question as shown below:<br><br>
         $default_ques<br><br>
      ----------------------------------------
      _END;

//display a question to the user
display($connection, $username);

echo <<<_END
<form method = 'post' action ='userlogin.php' enctype='multipart/form-data'>
<p><input type='submit' name = 'logout' value='Logout'></p>
</form>
_END;

if (isset($_POST['logout'])) {
    destroy_session_and_data();
}

$connection->close();


function display($connection, $username)
{
    echo <<<_END
    <table border = "1" cellspacing="2" cellpadding="2">
    <tr>
    <td><font face = "Arial">Name</font></td>
    <td><font face = "Arial">Question</font></td>
    </tr>
    _END;

    //display the query result --- full content
    $stmt = $connection->prepare('SELECT username,file_content,file_path FROM input WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    //display three lines 
    while ($row = $result->fetch_array()) {
        $filename = $row['file_path'];
        // $line_number = count(file($filename));
        //select a random question from the file content
        $random_1 = random_que($filename);
        $random_pre = $random_1;

        if (!isset($_POST['pop'])) {
            echo "<tr>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>" . $random_1 . "</td>";
            echo "</tr>";
            // $line_number--;
        } else {
            $random_2 = random_que($filename);
            echo "<tr>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>" . $random_2 . "</td>";
            echo "</tr>";
        }
    }

    echo "</table>";
    echo <<<_END
    <br><br>
    <form method = 'post' action ='userupload.php' enctype='multipart/form-data'>
    <p><input type='submit' name = 'pop' value='Pop a quesiton'></p><br><br>
    </form>
    _END;
}


function toMysql($path, $username, $filename, $connection)
{
    //extract conmtents from the file
    $fh = fopen("$path", "r");
    $data = "";

    if ($fh == false) {
        die("couldn't read the file.");
    }

    //read full contetn
    while (!feof($fh)) {

        $data .= fgetc($fh);
    }
    fclose($fh);

    //start the sql query 
    $stmt = $connection->prepare('INSERT INTO input VALUES (?,?,?,?)');
    $stmt->bind_param('ssss', $username, $filename, $data, $path);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo "Number of " . $stmt->affected_rows . " rows inserted.<br><br>";
    } else {
        echo "failed to access database.";
    }


    $stmt->close();
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

function destroy_session_and_data()
{
    $_SESSION = array();
    setcookie(session_name(), '', time() - 2592000, '/');
    session_destroy();
}

function different_user()
{
    echo <<<_END
    There are technical errors encountered.<br>
    Please login again.<br>
    _END;

    destroy_session_and_data();

    echo "Please <a href='userlogin.php'>click here</a> to log in.";
}

function random_que($filename)
{
    $lines = file($filename);
    $random_q = $lines[array_rand($lines)];
    return $random_q;
}
