<?php
require_once __DIR__ . "/../config.php";

global $logger;
global $errorHandler;
global $sessionHandler;
global $accessControlManager;
global $validator;

// If POST vars are set it means that a POST form has been submitted 
if (checkFormData(['email', 'otp', 'password', 'repeat_password'])) {

    // Protect against XSS
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $otp = filter_input(INPUT_POST, 'otp', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $passwordSubmitted = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $repeatPassword = filter_input(INPUT_POST, 'repeat_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Protect against XSRF
    if (!$token || $token !== $_SESSION['token']) {
        // return 405 http status code
        $accessControlManager->redirectIfXSRFAttack();
    } else {
        try {
            // Checks if passwords are the same
            if ($passwordSubmitted !== $repeatPassword) {
                throw new Exception('The inserted passwords do not match');
            } else {
                // Get all security info from db to verify user
                $result = getSecurityInfo($email);
                if ($result) {
                    $userSecurityInfo = $result->fetch_assoc();
                    if ($userSecurityInfo !== null && $result->num_rows === 1) {
                        // Checks OTP retrieved from db
                        if ($userSecurityInfo['otp'] !== null and $userSecurityInfo['lastOtp'] !== null) {
                            // Convert time for comparison
                            $lastOtpTime = strtotime($userSecurityInfo['lastOtp']);
                            $currentTime = time();

                            // In the db is stored the hash of the OTP,
                            // so the OTP given by the user needs to be hashed in order to be checked
                            $otpHashed = hash('sha256', $otp . $userSecurityInfo['salt']);

                            // Checks OTP inserted with the one in the db
                            if (($currentTime - $lastOtpTime) > 90 or $userSecurityInfo['otp'] !== $otpHashed) {
                                throw new Exception('The OTP is incorrect and/or expired for the user: ' . $email);
                            }

                            // check new password validation
                            if ($validator->checkPasswordStrength($passwordSubmitted, $email, $userSecurityInfo['username'], $userSecurityInfo['name'], $userSecurityInfo['surname'])) {

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
                  action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/passwordRecovery.php') ?>" method="POST">
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
                           title="Must contain at least one number, one uppercase letter, one lowercase letter, and at least 9 or more characters"
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
            <p class="mt-3 text-center"><a href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/otpRequest.php') ?>"
                                           class="no-otp">I don't have an Otp</a></p>
        </div>
    </div>
</div>

</body>
</html>
