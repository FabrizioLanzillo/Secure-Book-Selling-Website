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

try{

    if(checkFormData(['fullName', 'address', 'city', 'province', 'cap', 'country'])){

        $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
        $fullName = htmlspecialchars($_POST['fullName'], ENT_QUOTES, 'UTF-8');
        $address = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');
        $city = htmlspecialchars($_POST['city'], ENT_QUOTES, 'UTF-8');
        $province = htmlspecialchars($_POST['province'], ENT_QUOTES, 'UTF-8');
        $cap = htmlspecialchars($_POST['cap'], ENT_QUOTES, 'UTF-8');
        $country = htmlspecialchars($_POST['country'], ENT_QUOTES, 'UTF-8');

        if (!$token || $token !== $_SESSION['token']) {
            // return 405 http status code
            header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
            exit;
        } else {
            $sessionHandler->saveShippingInfo($fullName, $address, $city, $province, $cap, $country);
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
        <link rel="stylesheet" href="../../css/bootstrap.css">
    </head>
    <body>
        <?php
        include "../layout/header.php";
        ?>
        <section class="p-4 p-md-5 m-5 bg-info">
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

                                <button type="submit" class="btn btn-success btn-lg btn-block">Continue to Payment</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>
</html>
