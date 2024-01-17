<?php
require_once __DIR__ . "/../../config.php";

global $logger;
global $errorHandler;
global $sessionHandler;
global $shoppingCartHandler;
global $accessControlManager;

// Check path manipulation and broken access control
// Check if the user is logged
$accessControlManager->redirectIfAnonymous();
// Check if an admin tries to access this page
$accessControlManager->redirectIfAdmin();

try {
    // If POST vars are set it means that a POST form has been submitted 
    if (checkFormData(['CardHolderName', 'CardNumber', 'Expire', 'CVV'])) {

        // Protect against XSS
        $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $cardHolderName = filter_input(INPUT_POST, 'CardHolderName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $cardNumber = filter_input(INPUT_POST, 'CardNumber', FILTER_SANITIZE_NUMBER_INT);
        $expire = filter_input(INPUT_POST, 'Expire', FILTER_SANITIZE_NUMBER_INT);
        $CVV = filter_input(INPUT_POST, 'CVV', FILTER_SANITIZE_NUMBER_INT);

        // Protect against XSRF
        if (!$token || $token !== $_SESSION['token']) {
            // return 405 http status code
            $accessControlManager ->redirectIfXSRFAttack();
        } else {
            // Save card information in $_SESSION and redirect depending on $_SESSION vars set
            $sessionHandler->saveCreditCardInfo($cardHolderName, $cardNumber, $expire, $CVV);
            $logger->writeLog('INFO', "User: " . $_SESSION['email'] . " successfully set his payment info");
            $accessControlManager->routeMultiStepCheckout();
        }
    }
}
catch (Exception $e) {
    $logger->writeLog('ERROR', $e->getMessage());
    $errorHandler->handleException($e);
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payment Method</title>
        <script type="text/javascript" src="../../js/payment.js"></script>
        <link rel="stylesheet" href="../../css/bootstrap.css">

    </head>
    <body>
        <?php
        include "../layout/header.php";
        ?>
        <section class="p-4 p-md-5 m-5 ">
            <div class="row d-flex justify-content-center">
                <div class="col-md-10 col-lg-8 col-xl-6 w-100">
                    <div class="card rounded-4">
                        <div class="card-body p-4 ">
                            <div class="text-center mb-4">
                                <h3>Payment</h3>
                                <img class="img-fluid cc-img" src="../../img/creditcardicons.png" alt="creditcardicons">
                            </div>

                            <form name="paymentInfoForm" action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/user/paymentMethod.php');?>" method="POST">
                                <div id="cardOptions">
                                    <div class="form-outline mb-4">
                                        <label class="form-label" for="formControlLgXsd">Cardholder's Name</label>
                                        <input type="text" id="formControlLgXsd"
                                               name="CardHolderName"
                                               class="form-control form-control-lg"
                                               placeholder="Name Surname"
                                               title="Please Insert Name and Surname"
                                               pattern="[A-Za-z ]+" required>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-7">
                                            <div class="form-outline">
                                                <label class="form-label" for="formControlLgXM">Card Number</label>
                                                <input type="text" id="formControlLgXM"
                                                       name="CardNumber"
                                                       class="form-control form-control-lg"
                                                       placeholder="1234 5678 1234 5678"
                                                       maxlength="19"
                                                       pattern="[0-9]{4} [0-9]{4} [0-9]{4} [0-9]{4}"
                                                       title="Please enter a valid 16-digit card number divided by a space"
                                                       oninput="formatCardNumber(event)" required>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-outline">
                                                <label class="form-label" for="formControlLgExpk">Expire</label>
                                                <input type="text" id="formControlLgExpk"
                                                       name="Expire"
                                                       class="form-control form-control-lg"
                                                       placeholder="MM/YY"
                                                       pattern="\d{2}/\d{2}"
                                                       title="Please Insert this format MM/YY"
                                                       maxlength="5"
                                                       oninput="formatExpirationDate(event)"
                                                       onblur="checkExpirationDate(event.target.value)" required>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-outline">
                                                <label class="form-label" for="formControlLgcvv">CVV</label>
                                                <input type="password" id="formControlLgcvv"
                                                       name="CVV"
                                                       class="form-control form-control-lg"
                                                       placeholder="CVV"
                                                       pattern="[0-9]{3,4}"
                                                       title="Please enter a valid 3 or 4-digit CVV" required>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Hidden token to protect against XSRF -->
                                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block">Continue to Shipping Info</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>
</html>