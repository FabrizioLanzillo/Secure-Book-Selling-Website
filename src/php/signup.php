<?php
require_once __DIR__ . "/../config.php";

global $logger;
global $errorHandler;

// this block is executed only after the submit of the POST form
if(checkFormData(['name', 'surname', 'email', 'username', 'password', 'repeat_password', 'birthdate'])){
    
    // Protect against XSS
    $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $surname = htmlspecialchars($_POST['surname'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
    $repeatPassword = htmlspecialchars($_POST['repeat_password'], ENT_QUOTES, 'UTF-8');
    $birthdate = htmlspecialchars($_POST['birthdate'], ENT_QUOTES, 'UTF-8');

    if (!$token || $token !== $_SESSION['token']) {
        // return 405 http status code
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    } else {
        try{
            if ($password !== $repeatPassword){
                throw new Exception('The inserted passwords don\'t match');
            }
            else{
                $salt = bin2hex(random_bytes(32));
                $hashedPassword = hash('sha256', $password . $salt);
    
                $userData = array(
                    $username,
                    $hashedPassword,
                    $salt,
                    $email,
                    $name,
                    $surname,
                    $birthdate,
                    0,
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="../css/signup.css">
    <script src="../js/utilityFunction.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>
    <title>Book Selling - Sign Up</title>
</head>
<body>
    <?php
        include "./layout/header.php";
    ?>

    <div class="signup_container">
        <h2>Sign up</h2>
        <form name="sign_up" action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/signup.php') ?>" method="POST">
            <label><b>Name</b>
                <input class="signup_form_input" type="text" placeholder="Name" name="name" required>
            </label>

            <label><b>Surname</b>
                <input class="signup_form_input" type="text" placeholder="Surname" name="surname" required>
            </label>

            <label><b>Email</b>
                <input class="signup_form_input" type="email" placeholder="Email" name="email" required>
            </label>

            <label><b>Username</b>
                <input class="signup_form_input" type="text" placeholder="Username" name="username" required>
            </label>

            <label><b>Password</b>
                <input class="signup_form_input" type="password" placeholder="Password" name="password" id="password"
                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{9,}" title="Deve contenere almeno un numero, una lettera maiuscola, una lettera minuscola e almeno 8 o più caratteri"
                    required oninput="checkPasswordStrength('signup_button')">
                <meter max="4" id="password-strength-meter"></meter>
                <p id="password-strength-text"></p>
            </label>

            <label><b>Repeat password</b>
                <input class="signup_form_input" type="password" placeholder="Repeat Password" name="repeat_password" required>
            </label>

            <label><b>Date of birth</b>
                <input class="signup_form_input" type="date" name="birthdate" required>
            </label>

            <!-- Hidden token to protect against CSRF -->
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">

            <button class="signup_form_button" id="signup_button" type="submit">Sign up</button>
        </form>
    </div>
</body>
</html>
