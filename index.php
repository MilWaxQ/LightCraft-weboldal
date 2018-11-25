<!--
  This is a demo page for AuthMe website integration.
  See AuthMeController.php and the extending classes for the PHP code you need.
-->
<!DOCTYPE html>
<html lang="en">
 <head>
    <link href="/website/style.css" type="text/css" rel="stylesheet" />
   <title>Regisztráció</title>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 </head>
 <body>
 <center>
<?php
error_reporting(E_ALL);

require 'AuthMeController.php';

// Change this to the file of the hash encryption you need, e.g. Bcrypt.php or Sha256.php
require 'Sha256.php';
// The class name must correspond to the file you have in require above! e.g. require 'Sha256.php'; and new Sha256();
$authme_controller = new Sha256();

$action = get_from_post_or_empty('action');
$user = get_from_post_or_empty('username');
$pass = get_from_post_or_empty('password');
$email = get_from_post_or_empty('email');

$was_successful = false;
if ($action && $user && $pass) {
    if ($action === 'Log in') {
        $was_successful = process_login($user, $pass, $authme_controller);
    } else if ($action === 'Regisztráció') {
        $was_successful = process_register($user, $pass, $email, $authme_controller);
    }
}

if (!$was_successful) {
    echo '
	<div>
	<h1 class="registre">Regisztráció</h1>
Írd be a játékbeli neved, válassz egy tetszőleges jelszót, majd kattints a "Regisztráció" gombra!
<form method="post">
</div>
 <table>
   <tr><td>Felhasználónév</td><td><input style="border: 5px outset orange" type="text" placeholder="  Ide írd a felhasználóneved" value="' . htmlspecialchars($user) . '" name="username" /></td></tr>
   <tr><hr width=800px /><td><p style="margin-left: 32px;">Jelszó</p></td><td><input style="border: 5px outset orange" type="password" placeholder="  Ide írd a jelszavad" value="' . htmlspecialchars($pass) . '" name="password" /></td></tr>
   <tr>
     <td><input type="submit" name="action" value="Regisztráció" style="border:none;background-color:orange;margin-left:150px;font-size:16pt;"/></td>
   </tr>
 </table>
</form>';
}

function get_from_post_or_empty($index_name) {
    return trim(
        filter_input(INPUT_POST, $index_name, FILTER_UNSAFE_RAW, FILTER_REQUIRE_SCALAR | FILTER_FLAG_STRIP_LOW)
            ?: '');
}


// Login logic
function process_login($user, $pass, AuthMeController $controller) {
    if ($controller->checkPassword($user, $pass)) {
        printf('<h1>Hello, %s!</h1>', htmlspecialchars($user));
        echo 'Successful login. Nice to have you back!'
            . '<br /><a href="index.php">Back to form</a>';
        return true;
    } else {
        echo '<h1>Hiba</h1> Rossz név vagy jelszó.';
    }
    return false;
}

// Register logic
function process_register($user, $pass, $email, AuthMeController $controller) {
    if ($controller->isUserRegistered($user)) {
        echo '<h1>Hiba</h1> Már van ilyen nevű játékos.';
    } else if (!is_email_valid($email)) {
        echo '<h1>Error</h1> The supplied email is invalid.';
    } else {
        // Note that we don't validate the password or username at all in this demo...
        $register_success = $controller->register($user, $pass, $email);
        if ($register_success) {
            echo '<h1>Sikeres regisztráció</h1>A regisztráció 2-3 percet vesz igénybe!';
            echo '<br /><a href="index.php">Vissza a regisztrációhoz</a>';
            return true;
        } else {
            echo '<h1>Error</h1>Unfortunately, there was an error during the registration.';
        }
    }
    return false;
}

function is_email_valid($email) {
    return trim($email) === ''
        ? true // accept no email
        : filter_var($email, FILTER_VALIDATE_EMAIL);
}

?>
  </center>  
 </body>
</html>
