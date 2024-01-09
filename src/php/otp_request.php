<?php
    require_once __DIR__ . "/../config.php";

    global $logger;
    global $errorHandler;
    global $emailSender;
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
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="../css/otp_request.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <title>Book Selling - Otp Request</title>
    </head>
	<body>
        <?php
                include "./layout/header.php";
        ?>

        <div class="otp_request_container">
            <h2>Insert your email to receive an Otp</h2>
            <label><b>Email</b>
                <input class="email_input" type="email" placeholder="Email" name="email" required>
            </label>

            <button class="gen_otp_button" type="button">Generate OTP</button>
<!--        <div class="message"></div>-->
            <a href="//<?php echo SERVER_ROOT. '/php/password_recovery.php'?>" class="already-otp" >I already have an Otp</a>
        </div>
        <script>
            $(document).ready(function(){
                $(".gen_otp_button").click(function(){
                    const email = $(".email_input").val();
                    $(".gen_otp_button").prop("disabled", true).css("background-color", "grey").css("pointer-events", "none");
                    $.post( "//<?php echo SERVER_ROOT. '/php/otp_request.php'?>",
                            {email: email},
                            function(){
                                // $(".message").html("<p>Your OTP has been sent successfully!</p>");
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