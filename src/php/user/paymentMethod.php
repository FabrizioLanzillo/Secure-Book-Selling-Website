<?php
require_once __DIR__ . "/../../config.php";

global $logger;
global $errorHandler;
global $sessionHandler;
global $shoppingCartHandler;
global $accessControlManager;

// This function checks if the user is anonymous or not
// if in case, the user will be redirected to the login
$accessControlManager->redirectIfAnonymous();

try {
    if (checkFormData(['CardHolderName', 'CardNumber', 'Expire', 'CVV'])) {

        $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
        $cardHolderName = htmlspecialchars($_POST['CardHolderName'], ENT_QUOTES, 'UTF-8');
        $cardNumber = htmlspecialchars($_POST['CardNumber'], ENT_QUOTES, 'UTF-8');
        $expire = htmlspecialchars($_POST['Expire'], ENT_QUOTES, 'UTF-8');
        $CVV = htmlspecialchars($_POST['CVV'], ENT_QUOTES, 'UTF-8');

        if (!$token || $token !== $_SESSION['token']) {
            // return 405 http status code
            $accessControlManager ->redirectIfXSRFAttack();
        } else {
            $sessionHandler->saveCreditCardInfo($cardHolderName, $cardNumber, $expire, $CVV);
            $accessControlManager->routeMultiStepCheckout();
        }
    }
}
catch (Exception $e) {
    $errorHandler->handleException($e);
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Your Web Page Title</title>
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
                                                       oninput="formatExpirationDate(event)" required>
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
                                    <!-- Hidden token to protect against CSRF -->
                                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">
                                    <button type="submit" class="btn btn-success btn-lg btn-block">Continue to Shipping Info</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>
</html>