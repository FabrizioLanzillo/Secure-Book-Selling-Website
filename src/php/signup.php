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
<!--    <link rel="stylesheet" type="text/css" href="../css/signup.css">-->
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <script src="../js/utilityFunction.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>
    <title>Book Selling - Sign Up</title>
</head>
<body>
    <?php
        include "./layout/header.php";
    ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="signup-container">
                    <h2 class="text-center">Sign up</h2>
                    <form name="sign_up" action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/signup.php'); ?>" method="POST">
                        <div class="form-group">
                            <label for="name"><b>Name</b></label>
                            <input class="form-control" type="text" placeholder="Name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="surname"><b>Surname</b></label>
                            <input class="form-control" type="text" placeholder="Surname" name="surname" required>
                        </div>

                        <div class="form-group">
                            <label for="email"><b>Email</b></label>
                            <input class="form-control" type="email" placeholder="Email" name="email" required>
                        </div>

                        <div class="form-group">
                            <label for="username"><b>Username</b></label>
                            <input class="form-control" type="text" placeholder="Username" name="username" required>
                        </div>

                        <div class="form-group">
                            <label for="password"><b>Password</b></label>
                            <input class="form-control" type="password" placeholder="Password" name="password" id="password"
                                   pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{9,}" title="Must contain at least one number, one uppercase letter, one lowercase letter, and at least 8 or more characters"
                                   required oninput="checkPasswordStrength('signup_button')">
                            <meter max="4" id="password-strength-meter"></meter>
                            <p id="password-strength-text"></p>
                        </div>

                        <div class="form-group">
                            <label for="repeat_password"><b>Repeat password</b></label>
                            <input class="form-control" type="password" placeholder="Repeat Password" name="repeat_password" required>
                        </div>

                        <div class="form-group mb-5">
                            <label for="birthdate"><b>Date of birth</b></label>
                            <input class="form-control" type="date" name="birthdate" required>
                        </div>

                        <!-- Hidden token to protect against CSRF -->
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">

                        <button class="btn btn-primary btn-block mb-5" id="signup_button" type="submit">Sign up</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
