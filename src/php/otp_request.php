<?php
    require_once __DIR__ . "/../config.php";

    global $logger;
    global $errorHandler;
    global $emailSender;
    $sentOtpEmail = false;

    // this block is executed only after submit of the POST form
    if (isset($_POST['email'])) {
        $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');

        if (!$token || $token !== $_SESSION['token']) {
            // return 405 http status code
            header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
            exit;
        } else {
            try {
                $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
                $retrievedTime = getOtpTimeInformation($email);
                if ($retrievedTime === null)
                    throw new Exception('Error retrieving the last Otp generated');
                $lastOtpTime = strtotime($retrievedTime);
                $currentTime = time();

                if ($lastOtpTime !== false) {
                    if(($currentTime-$lastOtpTime) > 90) {     // 1 minute and 30 sec
                        $newOtp = generateRandomString(8);
                        if (setOtp($email, $newOtp)) {
                            if ($emailSender->sendEmail($email,
                                                        "BookSelling - Your OTP code",
                                                        "OTP Request",
                                                        "This is the otp: $newOtp requested.", "It will last only for 5 minutes.") !== false){

                                $logger->writeLog('INFO', "2FA check for the user: " . $email . " OTP has been created successfully");
                                $sentOtpEmail = true;
                            } else {
                                throw new Exception("Couldn't send an email to the specified email");
                            }
                        }
                    } else {
                        throw new Exception('You already received an Otp recently');
                    }
                } else {
                    throw new Exception('Error retrieving the last Otp generated');
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
<!--    <link rel="stylesheet" type="text/css" href="../css/otp_request.css">-->
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
                    <form name="otp_request" action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/password_recovery.php'); ?>" method="POST">
                        <div class="form-group m-auto w-75 ">
                            <label for="email" class="sr-only">Email</label>
                            <input class="form-control mb-4" type="email" placeholder="Email" name="email" required>
                            <!-- Hidden token to protect against CSRF -->
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">

                            <button class="btn btn-primary btn-block" type="submit">Generate OTP</button>
                        </div>

                        <a href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/password_recovery.php'); ?>" class="btn btn-link btn-block mt-3">I already have an OTP</a>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function(){
            $(".gen_otp_button").click(function(){
                const email = $(".email_input").val();
                const token = $("[name='token']").val(); // Get the CSRF token from the hidden input

                $(".gen_otp_button").prop("disabled", true).css("background-color", "grey").css("pointer-events", "none");
                
                $.post(
                    "//<?php echo htmlspecialchars(SERVER_ROOT. '/php/otp_request.php') ?>",
                    { email: email, token: token }, // Include the CSRF token in the data object
                    function(){
                        alert("Your OTP has been sent successfully!");
                        $(".email_input").val("");
                        $(".gen_otp_button").prop("disabled", true).css("background-color", "#1982cf").css("pointer-events", "none");
                    }
                );
            });
        });
    </script>
</body>
</html>
