<?php
require_once __DIR__ . "/../config.php";

global $logger;
global $errorHandler;
global $emailSender;
global $accessControlManager;

// If one of POST vars is set it means that a POST form has been submitted
if (checkFormData(['email'])) {

    // Protect against XSS
    $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Protect against XSRF
    if (!$token || $token !== $_SESSION['token']) {
        // return 405 http status code
        $accessControlManager->redirectIfXSRFAttack();
    } else {
        try {
            // Check when the last otp has been requested
            $result = getOtpTimeInformation($email);
            if ($result) {
                $dataQuery = $result->fetch_assoc();
                if ($dataQuery !== null && $result->num_rows === 1) {
                    // we take the timestamp of the last otp generated
                    $lastOtpTime = strtotime($dataQuery['lastOtp']);
                    $currentTime = time();
                    // we check whether 90 seconds have elapsed
                    if (($currentTime - $lastOtpTime) > 90) {     // 1 minute and 30 sec
                        // we generate a new OTP
                        // Generates a random string of 8 chars
                        $newOTP = generateRandomString(8);
                        // In the db the OTP is stored hashed
                        $hashedNewOTP = hash('sha256', $newOTP);
                        // save in the db the OPT for the specified user
                        if (setOtp($email, $hashedNewOTP)) {
                            if ($emailSender->sendEmail($email,
                                    "BookSelling - Your OTP code",
                                    "OTP Request",
                                    "This is the otp: $newOTP requested.", "It will last only for 90 seconds.") !== false) {

                                $logger->writeLog('INFO', "The OTP was successfully created and sent to the user: " . $email);
                                header('Location: //' . SERVER_ROOT . '/php/passwordRecovery.php');
                                exit;
                            } else {
                                throw new Exception("Could not send an email to the specified email address: " . $email);
                            }
                        } else {
                            throw new Exception("Error during the creation of the OTP for this email: " . $email);
                        }
                    } else {
                        throw new Exception('OTP already sent recently to this e-mail address: ' . $email);
                    }
                } else {
                    throw new Exception('No Account found for the given email: ' . $email);
                }
            } else {
                throw new Exception('Error retrieving the last OTP generated for the given mail: ' . $email);
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>Book Selling - Otp Request</title>
</head>
<body>
<?php
include "./layout/header.php";
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="p-4 border rounded">
                <h2 class="text-center mb-5">Insert your email to receive an OTP</h2>
                <form name="otp_request"
                      action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/otpRequest.php'); ?>" method="POST">
                    <div class="form-group m-auto w-75 ">
                        <label for="email" class="sr-only">Email</label>
                        <input class="form-control mb-4" type="email" placeholder="Email" name="email" required>
                        <!-- Hidden token to protect against CSRF -->
                        <input type="hidden" name="token"
                               value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">

                        <button class="btn btn-primary btn-block" type="submit">Generate OTP</button>
                    </div>
                </form>
                <a href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/passwordRecovery.php'); ?>"
                   class="btn btn-link btn-block mt-3">I already have an OTP</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
