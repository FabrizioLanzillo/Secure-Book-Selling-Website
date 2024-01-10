<?php
require_once __DIR__ . "/../config.php";

global $logger;
global $errorHandler;

function checkFormData(): bool{
    $requiredFields = ['email', 'otp', 'password', 'repeat_password'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            return false;
        }
    }
    return true;
}

// this block is executed only after the submit of the POST form
if(checkFormData()){
    $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');

    if (!$token || $token !== $_SESSION['token']) {
        // return 405 http status code
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    } else {
        try{
            if ($_POST['password'] !== $_POST['repeat_password']) {
                throw new Exception('The inserted passwords don\'t match');
            }
            else {
                $resultQuery = getSecurityInfo($_POST['email']);
                $otpData = $resultQuery->fetch_assoc();
    
                if($otpData['otp'] === null OR $otpData['lastOtp'] === null)
                    throw new Exception('An error occured in your request');
    
                $lastOtpTime = strtotime($otpData['lastOtp']);
                $currentTime = time();
    
                if(($currentTime-$lastOtpTime) > 300 OR $otpData['otp'] !== $_POST['otp'])
                    throw new Exception('The Otp is incorrect and/or expired');
    
                $salt = bin2hex(random_bytes(32));
                $hashedPassword = hash('sha256', $_POST['password'] . $salt);
    
                $userData = array(
                    $hashedPassword,
                    $salt,
                    $_POST['email'],
                );
    
                $result = updateUserPassword($userData);
                if($result){
                    $logger->writeLog('INFO', "The password update of the user: ".$userData[2].", Succeeded");
                    header('Location: //' . SERVER_ROOT . '/php/login.php');
                    exit;
                }
                else{
                    throw new Exception('Couldn\'t update the user\'s password');
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
        <link rel="stylesheet" type="text/css" href="../css/password_recovery.css">
        <script src="../js/utilityFunction.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>
        <title>Book Selling - Password recovery</title>
    </head>
    <body>
        <?php
            include "./layout/header.php";
        ?>

        <div class="pwd_recovery_container">
            <h2>Password recovery</h2>
            <form name = "pwd_recovery" action="//<?php echo SERVER_ROOT. '/php/password_recovery.php'?>" method="POST">
                <label><b>Email</b>
                    <input class="pwd_recovery_input" type="email" placeholder="Email" name="email" required>
                </label>

                <label><b>Otp</b>
                    <input class="pwd_recovery_input" type="text" placeholder="Otp code" name="otp" required>
                </label>

                <label><b>Password</b>
                    <input class="pwd_recovery_input" type="password" placeholder="Password" name="password" id="password"
                           pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{9,}" title="Deve contenere almeno un numero, una lettera maiuscola, una lettera minuscola e almeno 8 o più caratteri"
                           required oninput="checkPasswordStrength('change_psw_button')">
                    <meter max="4" id="password-strength-meter"></meter>
                    <p id="password-strength-text"></p>
                    <p id="suggestions"></p>
                </label>

                <label><b>Repeat password</b>
                    <input class="pwd_recovery_input" type="password" placeholder="Repeat Password" name="repeat_password" required>
                </label>

                <!-- Hidden token to protect against CSRF -->
                <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">

                <button class="pwd_recovery_button" id="change_psw_button" type="submit">Change password</button>
            </form>
            <a href="//<?php echo SERVER_ROOT. '/php/otp_request.php'?>" class="no-otp" >I don't have an Otp</a>
        </div>
    </body>
</html>