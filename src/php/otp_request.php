<?php
    require_once __DIR__ . "./../config.php";
    require_once __DIR__ . "/util/dbInteraction.php";
    require_once __DIR__ . "/util/emailSender.php";

    global $logger;
    global $errorHandler;
    $sentOtpEmail = false;

    // this block is executed only after submit of the POST form
    if (isset($_POST['email'])) {
        try {
            $email = $_POST['email'];
            $retrievedTime = getOtpTimeInformation($email);
            if ($retrievedTime === null)
                throw new Exception('Error retrieving the last Otp generated');
            $lastOtpTime = strtotime($retrievedTime);
            $currentTime = time();

            if ($lastOtpTime !== false) {
                if(($currentTime-$lastOtpTime) > 300) {     //5 minutes
                    $newOtp = $salt = bin2hex(random_bytes(32));
                    if (setOtp($email, $newOtp)) {
                        if (sendOtpEmail($email, $newOtp) !== false){
                            $logger->writeLog('INFO', "New Otp for the user: " . $email . " has been created successfully");
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
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="../css/otp_request.css">
        <title>Book Selling - Otp Request</title></head>
	<body>
        <?php
                include "./layout/header.php";
        ?>
        
        <div class="otp_request_container">
            <h2>Insert your email to receive an Otp</h2>
            <form name = "otp_request" action="//<?php echo SERVER_ROOT. '/php/otp_request.php'?>" method="POST">
                <label><b>Email</b>
                    <input class="email_input" type="email" placeholder="Email" name="email" required>
                </label>

                <button class="gen_otp_button" type="submit">Generate otp</button>
                <?php if ($sentOtpEmail): ?>
                    <div class="message">
                        <p>Your OTP has been sent successfully!</p>
                    </div>
                <?php endif; ?>
            </form>
            <a href="//<?php echo SERVER_ROOT. '/php/password_recovery.php'?>" class="already-otp" >I already have an Otp</a>
        </div>
	</body>
</html>