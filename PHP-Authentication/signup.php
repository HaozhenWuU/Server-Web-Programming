<?php

echo <<<_END
    <form method = 'post' action ='signup.php' enctype='multipart/form-data'>
    <h1>Sign Up</h1>
    <p>Please fill in this form to create an account.</p>
    <hr>

    <label for="username"><b>Username</b></label>
    <input type="text" placeholder="Enter Username" name="u_name" id="username" required>
    <label for="psw"><b>Password</b></label>
    <input type="password" placeholder="Enter Password" name="psw" id="psw" required>
    
    <p><input type='submit' name = 'submit' value='submit'></p>
    </form>
 
_END;

//handle the inputs

if (isset($_POST['submit'])) {

    $connection = getConnection();
    $username = sanitizeMySQL($connection, $_POST['u_name']);
    $psw = sanitizeMySQL($connection, $_POST['psw']);
    $salt = random_str();
    $token = hash('ripemd128', "$salt$psw");
    addUser($connection, $username, $salt, $token);
    $connection->close();
    header("location: userlogin.php");
}

function addUser($conn, $u_name, $salt, $token)
{
    $var = NULL;
    $stmt = $conn->prepare('INSERT INTO credentials VALUES(?,?,?,?)');
    $stmt->bind_param('ssss', $var, $u_name, $salt, $token);
    $stmt->execute();
    $stmt->close();
}

function random_str()
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';

    for ($i = 0; $i < 10; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }

    return $randomString;
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
