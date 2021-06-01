<?php

//establish connection
$connection = getConnection();


echo <<<_END
    <form method = 'post' action ='userlogin.php' enctype='multipart/form-data'>
    <h1>Log In</h1>
    <p>Please Log in.</p>
    <hr>
    <label for="username"><b>Username</b></label>
    <input type="text" placeholder="Enter Username" name="u_name" id="username" required>
    <label for="psw"><b>Password</b></label>
    <input type="password" placeholder="Enter Password" name="psw" id="psw" required>
    
    <p><input type='submit' name = 'submit' value='submit'></p>
    </form>
 
_END;

if (isset($_POST['u_name']) && isset($_POST['psw'])) {

    $username = sanitizeString($_POST['u_name']);
    $psw = sanitizeString($_POST['psw']);
    $salt = random_str();
    $token = hash('ripemd128', "$salt$psw");
    //query the username
    $stmt = $connection->prepare('SELECT username,salt,password FROM credentials WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        echo "Invalid combination of username and password.";
    }
    if ($result->num_rows > 0) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $salt = $row["salt"];
        $hash_pw = hash('ripemd128', "$salt$psw");

        if ($username == $row["username"] && $hash_pw == $row["password"]) {
            session_start();
            $_SESSION['uname'] = $username;
            $_SESSION['time_stamp'] = time();
            $_SESSION['check'] = hash('ripemd128', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
            echo "You are now logged in.</br></br>";
            echo "<b>Welcome " . $username . "</b></br></br>";
            echo "<p><a href=userupload.php>Click here to continue</a></p>";
        } else die("Invalid username / password combination");
    } else {
        header('WWW-Authenticate: Basic realm="Restricted Section"');
        header('HTTP/1.0 401 Unauthorized');
        die("Please enter your username and password");
    }
    $result->close();
    $stmt->close();
    $connection->close();
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
