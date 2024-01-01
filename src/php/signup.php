<?php
require_once __DIR__ . "./../config.php";
require_once __DIR__ . "/util/dbInteraction.php";

global $logger;
global $errorHandler;

function checkFormData(): bool{
    $requiredFields = ['name', 'surname', 'email', 'username', 'password', 'repeat_password', 'birthdate'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            return false;
        }
    }
    return true;
}

// this block is executed only after the submit of the POST form
if(checkFormData()){
    try{
        if ($_POST['password'] !== $_POST['repeat_password']){
            throw new Exception('The inserted passwords don\'t match');
        }
        else{
            $salt = bin2hex(random_bytes(32));
            $hashedPassword = hash('sha256', $_POST['password'] . $salt);

            $userData = array(
                $_POST['username'],
                $hashedPassword,
                $salt,
                $_POST['email'],
                $_POST['name'],
                $_POST['surname'],
                $_POST['birthdate'],
                0,
            );

            $result = insertUser($userData);
            if($result){
                $logger->writeLog('INFO', "Signup of the user: ".$userData[4].", Succeeded");
                header('Location: //' . SERVER_ROOT . '/php/login.php');
                exit;
            }
            else{
                throw new Exception('Couldn\'t register the user');
            }
        }
    } catch (Exception $e) {
        $errorHandler->handleException($e);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="../css/signup.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>
        <title>Book Selling - Sign Up</title>
    </head>
    <body>
        <?php
            include "./layout/header.php";
        ?>

        <div class="signup_container">
            <h2>Sign up</h2>
            <form name = "sign_up" action="//<?php echo SERVER_ROOT. '/php/signup.php'?>" method="POST">
                <label><b>Name</b>
                    <input class="signup_form_input" type="text" placeholder="Name" name="name" required>
                </label>

                <label><b>Surname</b>
                    <input class="signup_form_input" type="text" placeholder="Surname" name="surname" required>
                </label>

                <label><b>Email</b>
                    <input class="signup_form_input" type="text" placeholder="Email" name="email" required>
                </label>

                <label><b>Username</b>
                    <input class="signup_form_input" type="text" placeholder="Username" name="username" required>
                </label>

                <label><b>Password</b>
                    <input class="signup_form_input" type="password" placeholder="Password" name="password" id="password" required oninput="checkPasswordStrength()">
                    <meter max="4" id="password-strength-meter"></meter>
                    <p id="password-strength-text"></p>
                </label>


                <label><b>Repeat password</b>
                    <input class="signup_form_input" type="password" placeholder="Repeat Password" name="repeat_password" required>
                </label>

                <label><b>Date of birth</b>
                    <input class="signup_form_input" type="date" name="birthdate" required>
                </label>

                <button class="signup_form_button" type="submit">Sign up</button>
            </form>
        </div>
        <script>
            function checkPasswordStrength() {
                var password = document.getElementById("password").value;
                var result = zxcvbn(password);

                // Update password strength meter
                document.getElementById("password-strength-meter").value = result.score;

                // Update password strength text
                var text = "";
                switch (result.score) {
                    case 0:
                        text = "Very Weak";
                        break;
                    case 1:
                        text = "Weak";
                        break;
                    case 2:
                        text = "Moderate";
                        break;
                    case 3:
                        text = "Strong";
                        break;
                    case 4:
                        text = "Very Strong";
                        break;
                    default:
                        break;
                }
                document.getElementById("password-strength-text").innerHTML = text;
            }
        </script>

    </body>
</html>