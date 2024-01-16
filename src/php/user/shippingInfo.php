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

try{
    // If POST vars are set it means that a POST form has been submitted 
    if(checkFormData(['fullName', 'address', 'city', 'province', 'cap', 'country'])){

        // Protect against XSS
        $token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $fullName = filter_input(INPUT_POST, 'fullName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $province = filter_input(INPUT_POST, 'province', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $cap = filter_input(INPUT_POST, 'cap', FILTER_SANITIZE_NUMBER_INT);
        $country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Protect against XSRF
        if (!$token || $token !== $_SESSION['token']) {
            // return 405 http status code
            $accessControlManager ->redirectIfXSRFAttack();
        } else {
            // Save cart information in $_SESSION and redirect depending on $_SESSION vars set
            $sessionHandler->saveShippingInfo($fullName, $address, $city, $province, $cap, $country);
            $logger->writeLog('INFO', "User: " . $_SESSION['email'] . " successfully set his shipping info");
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
        <title>Your Web Page Title</title>
        <link rel="stylesheet" href="../../css/bootstrap.css">
    </head>
    <body>
        <?php
        include "../layout/header.php";
        ?>
        <section class="p-4 p-md-5 m-5">
            <div class="row d-flex justify-content-center">
                <div class="col-md-10 col-lg-8 col-xl-5 w-100">
                    <div class="card rounded-4">
                        <div class="card-body p-4 ">
                            <div class="text-center mb-4">
                                <h3>Shipping Information</h3>
                            </div>

                            <form name="shippingInfoForm" action="//<?php echo htmlspecialchars( SERVER_ROOT . '/php/user/shippingInfo.php');?>" method="POST">

                                <div class="form-outline mb-4">
                                    <label class="form-label" for="formControlLgXsd">Full Name</label>
                                    <input type="text" id="formControlLgXsd"
                                           class="form-control form-control-lg"
                                           placeholder="Name Surname"
                                           name = "fullName"
                                           title="Please Insert Name and Surname"
                                           pattern="[A-Za-z ]+" required >
                                </div>


                                <div class="form-outline mb-4">
                                    <label class="form-label" for="formControlLgXM">Address</label>
                                    <input type="text" id="formControlLgXM"
                                           class="form-control form-control-lg"
                                           placeholder="Street Address"
                                           name = "address"
                                           title="Please insert an Address"
                                           required >
                                </div>

                                <div class="row mb-4">
                                    <div class="col-6">
                                        <div class="form-outline">
                                            <label class="form-label" for="formControlLgCity">City</label>
                                            <input type="text" id="formControlLgCity"
                                                   class="form-control form-control-lg"
                                                   placeholder="City"
                                                   name = "city"
                                                   title="Please insert a City"
                                                   pattern="[A-Za-z ]+" required>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-outline">
                                            <label class="form-label" for="formControlLgState">Province</label>
                                            <input type="text" id="formControlLgState" class="form-control form-control-lg"
                                                   placeholder="Province"
                                                   pattern="[A-Z]{2}"
                                                   name = "province"
                                                   title="Please enter exactly two uppercase letters for the Province"
                                                   required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-6">
                                        <div class="form-outline">
                                            <label class="form-label" for="formControlLgZip">CAP</label>
                                            <input type="text" id="formControlLgZip" class="form-control form-control-lg"
                                                   placeholder="CAP"
                                                   pattern="[0-9]{5}"
                                                   name = "cap"
                                                   title="Please enter a valid 5-digit CAP code"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-outline">
                                            <label class="form-label" for="formControlLgCountry">Country</label>
                                            <input type="text" id="formControlLgCountry" class="form-control form-control-lg"
                                                   placeholder="Country"
                                                   name = "country"
                                                   title="Please insert a Country"
                                                   pattern="[A-Za-z ]+" required >
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden token to protect against CSRF -->
                                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">
                                <button type="submit" class="btn btn-primary btn-lg btn-block">Continue to Payment</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>
</html>
