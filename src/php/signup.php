<?php
require_once __DIR__ . "/../config.php";

global $logger;
global $errorHandler;
global $accessControlManager;
global $emailSender;
global $sessionHandler;
global $validator;


// check if the user is logged or not, if the user is logged the access to the signup page is forbidden
// and the user will be redirected to the home page
if ($sessionHandler->isLogged()) {
    $accessControlManager->redirectToHome();
}

// If POST vars are set it means that a POST form has been submitted 
if (checkFormData(['name', 'surname', 'email', 'username', 'password', 'repeat_password'])) {

    // Protect against XSS
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $surname = filter_input(INPUT_POST, 'surname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $repeatPassword = filter_input(INPUT_POST, 'repeat_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Protect against XSRF
    if (!$token || $token !== $_SESSION['token']) {
        // return 405 http status code
        $accessControlManager->redirectIfXSRFAttack();
    } else {
        try {
            // Checks if passwords are the same
            if ($password !== $repeatPassword) {
                throw new Exception('The inserted passwords don\'t match');
            } else {
                if($validator->checkPasswordStrength($password, $email, $username, $name, $surname)) {

                    // Generates new vars to insert in the db
                    $salt = bin2hex(random_bytes(32));
                    $hashedPassword = hash('sha256', $password . $salt);

                    $userData = array(
                        $username,
                        $hashedPassword,
                        $salt,
                        $email,
                        $name,
                        $surname,
                        0,
                    );

                    // Inserts user's info in the db
                    if (insertUser($userData)) {
                        $logger->writeLog('INFO', "Signup of the user: " . $email . ", Succeeded");
                        if ($emailSender->sendEmail($email,
                                "BookSelling - Welcome",
                                "Signup is successfully completed",
                                "Welcome in the bookselling community.", "Thank you for your support!") === false) {
                            $logger->writeLog('ERROR', "Error during the send of the Signup Email");
                        }
                        header('Location: //' . SERVER_ROOT . '/php/login.php');
                        exit;
                    } else {
                        // No need to send a logger because it enters here only if db fails
                        throw new Exception('Could not register the user');
                    }
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
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
<!--    <script src="../js/utilityFunction.js"></script>-->
<!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>-->
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
                <form name="sign_up" action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/signup.php'); ?>"
                      method="POST">
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
                               pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{9,}"
                               title="Must contain at least one number, one uppercase letter, one lowercase letter, and at least 9 or more characters"
                               required oninput="checkPasswordStrength()">
                        <meter max="4" id="password-strength-meter"></meter>
                        <p id="password-strength-text"></p>
                    </div>

                    <div class="form-group">
                        <label for="repeat_password"><b>Repeat password</b></label>
                        <input class="form-control" type="password" placeholder="Repeat Password" name="repeat_password"
                               required>
                    </div>

                    <!-- Hidden token to protect against XSRF -->
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">

                    <button class="btn btn-primary btn-block mb-5" id="signup_button" type="submit">Sign up</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
