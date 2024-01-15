<?php
require_once __DIR__ . "/../config.php";

global $logger;
global $errorHandler;
global $emailSender;
global $accessControlManager;

// If one of POST vars is set it means that a POST form has been submitted
if (checkFormData(['email'])) {

    // Protect against XSS
    $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $logger->writeLog('INFO', "Protection against XSS applied");

    // Protect against XSRF
    if (!$token || $token !== $_SESSION['token']) {
        // return 405 http status code
        $accessControlManager->redirectIfXSRFAttack();
    } else {
        $logger->writeLog('INFO', "XSRF control passed");
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
                        $newOtp = generateRandomString(8);
                        // save in the db the OPT for the specified user
                        if (setOtp($email, $newOtp)) {
                            if ($emailSender->sendEmail($email,
                                    "BookSelling - Your OTP code",
                                    "OTP Request",
                                    "This is the otp: $newOtp requested.", "It will last only for 90 seconds.") !== false) {

                                $logger->writeLog('INFO', "2FA check for the user: " . $email . " OTP has been created successfully");
                                header('Location: //' . SERVER_ROOT . '/php/passwordRecovery.php');
                                exit;
                            } else {
                                throw new Exception("Couldn't send an email to the specified email address: ". $email);
                            }
                        } else {
                            throw new Exception("Error during the creation of the OTP for the email: ". $email);
                        }
                    } else {
                        throw new Exception('OTP recently sent for the mail: '. $email);
                    }
                } else {
                    throw new Exception('No Account found for the given email: '. $email);
                }
            } else {
                throw new Exception('Error retrieving the last OTP generated for the given mail: '. $email);
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
