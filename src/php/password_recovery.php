<?php
require_once __DIR__ . "/../config.php";

global $logger;
global $errorHandler;
global $sessionHandler;
global $accessControlManager;

// If POST vars are set it means that a POST form has been submitted 
if (checkFormData(['email', 'otp', 'password', 'repeat_password'])) {

    // Protect against XSS
    $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $otp = htmlspecialchars($_POST['otp'], ENT_QUOTES, 'UTF-8');
    $passwordSubmitted = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
    $repeatPassword = htmlspecialchars($_POST['repeat_password'], ENT_QUOTES, 'UTF-8');
    $logger->writeLog('INFO', "Protection against XSS applied");

    // Protect against XSRF
    if (!$token || $token !== $_SESSION['token']) {
        // return 405 http status code
        $accessControlManager->redirectIfXSRFAttack();
    } else {
        $logger->writeLog('INFO', "XSRF control passed");
        try {
            // Checks if passwords are the same
            if ($passwordSubmitted !== $repeatPassword) {
                throw new Exception('The inserted passwords don\'t match');
            } else {
                // Get all security info from db to verify user
                $result = getSecurityInfo($email);
                if ($result) {
                    $otpData = $result->fetch_assoc();
                    if ($otpData !== null && $result->num_rows === 1) {
                        // Checks OTP retrieved from db
                        if ($otpData['otp'] !== null and $otpData['lastOtp'] !== null) {
                            // Convert time for comparison
                            $lastOtpTime = strtotime($otpData['lastOtp']);
                            $currentTime = time();

                            // Checks OTP inserted with the one in the db
                            if (($currentTime - $lastOtpTime) > 90 or $otpData['otp'] !== $otp) {
                                throw new Exception('The OTP is incorrect and/or expired for the user: ' . $email);
                            }

                            // Generates new vars to insert in the db
                            $salt = bin2hex(random_bytes(32));
                            $hashedPassword = hash('sha256', $passwordSubmitted . $salt);

                            $userData = array(
                                $hashedPassword,
                                $salt,
                                $email,
                            );

                            // Update user's password
                            if (updateUserPassword($userData)) {
                                $logger->writeLog('INFO', "The password update of the user: " . $email . ", Succeeded");
                                header('Location: //' . SERVER_ROOT . '/php/login.php');
                                exit;
                            } else {
                                // No need to send a logger because it enters here only if db fails
                                throw new Exception('Could not update the password of the user: ' . $email);
                            }
                        } else {
                            throw new Exception('No OTP was generated for this user: ' . $email);
                        }
                    } else {
                        throw new Exception('No Account found for the given email: ' . $email);
                    }
                } else {
                    throw new Exception('Error retrieving the last OTP generated for the given email: ' . $email);
                }
            }
        } catch (Exception $e) {
            $logger->writeLog('ERROR', $e->getMessage());
            $errorHandler->handleException($e);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <script src="../js/utilityFunction.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>
    <title>Book Selling - Password recovery</title>
</head>
<body>
<?php
include "./layout/header.php";
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center mb-4">Password Recovery</h2>
            <form class="pwd-recovery-form"
                  action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/password_recovery.php') ?>" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label"><b>Email</b></label>
                    <input class="form-control" type="email" placeholder="Email" name="email" required>
                </div>

                <div class="mb-3">
                    <label for="otp" class="form-label"><b>OTP</b></label>
                    <input class="form-control" type="text" placeholder="OTP code" name="otp" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label"><b>Password</b></label>
                    <input class="form-control" type="password" placeholder="Password" name="password" id="password"
                           pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{9,}"
                           title="Must contain at least one number, one uppercase letter, one lowercase letter, and at least 8 or more characters"
                           required oninput="checkPasswordStrength()">
                    <meter max="4" id="password-strength-meter"></meter>
                    <p id="password-strength-text"></p>
                    <p id="suggestions"></p>
                </div>

                <div class="mb-3">
                    <label for="repeat_password" class="form-label"><b>Repeat password</b></label>
                    <input class="form-control" type="password" placeholder="Repeat Password" name="repeat_password"
                           required>
                </div>

                <!-- Hidden token to protect against CSRF -->
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">

                <button class="btn btn-primary" id="change_psw_button" type="submit">Change Password</button>
            </form>
            <p class="mt-3 text-center"><a href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/otp_request.php') ?>"
                                           class="no-otp">I don't have an Otp</a></p>
        </div>
    </div>
</div>

</body>
</html>
