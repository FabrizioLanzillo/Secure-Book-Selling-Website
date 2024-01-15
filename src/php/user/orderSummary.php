<?php
require_once __DIR__ . "/../../config.php";

global $sessionHandler;
global $shoppingCartHandler;
global $errorHandler;
global $emailSender;
global $logger;
global $accessControlManager;

// Check path manipulation and broken access control
// Check if the user is logged
$accessControlManager->redirectIfAnonymous();
// Check if an admin tries to access this page
$accessControlManager->redirectIfAdmin();

// This function checks if the user has already inserted payment info and shipping info
// if is not the case, the user will be redirected to the shopping cart page.
$accessControlManager->checkFinalStepCheckout();
$books = $shoppingCartHandler->getBooks();

try {
    // If one of POST vars is set it means that a POST form has been submitted 
    if (checkFormData(['totalPrice'])) {

        // Protect against XSS
        $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
        $totalPriceOrder = htmlspecialchars($_POST['totalPrice'], ENT_QUOTES, 'UTF-8');
        $logger->writeLog('INFO', "Protection against XSS applied");

        // Protect against XSRF
        if (!$token || $token !== $_SESSION['token']) {
            // return 405 http status code
            $accessControlManager->redirectIfXSRFAttack();
        } else {
            $logger->writeLog('INFO', "XSRF control passed");
            //Set the time and userId for the order's rows
            $currentTime = date('Y-m-d H:i:s');
            $userId = $_SESSION['userId'];
            // Perform query to add item in the db
            if (addItemToOrders($userId, $currentTime, $books, $totalPriceOrder)) {
                // Now the shopping cart needs to be emptied, since user performed the order
                $shoppingCartHandler->clearShoppingCart();
                if ($emailSender->sendEmail($_SESSION['email'],
                        "BookSelling - Successful Purchase",
                        "New Books Purchase",
                        "Your Purchase was successfully completed.",
                        "You will be able to download your e-books through the download buttons in the orders section, 
                                                    accessible after the login.",
                        "Thank you again for your purchase.") !== false) {

                    $logger->writeLog('INFO', "Purchase made by the user: " . $_SESSION['email'] . ", was Successful");
                    // Redirect to the home page
                    $accessControlManager->redirectToHome("paymentResponse", "OK");
                } else {
                    // Error sending the email
                    throw new Exception("Failed attempt to send payment confirmation email for user: " . $_SESSION['email']);
                }
            } else {
                // Error performing payment
                throw new Exception("Payment failed for user: " . $_SESSION['email']);
            }
        }
    }
} catch (Exception $e) {
    $logger->writeLog('ERROR', $e->getMessage());
    $accessControlManager->redirectToHome("paymentResponse", $e->getMessage());
}

// Var to display total price in the page
$totalPrice = 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary Page</title>
    <link rel="stylesheet" href="../../css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
</head>
<body>
<?php
include "./../layout/header.php";
?>
<section class="container mt-5 p-5 rounded ">

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Checkout</h2>
                </div>
                <div class="card-body">
                    <?php
                    if ($books !== null) {
                        ?>
                        <div class="table-responsive">
                            <table class="table table-bordered m-0">
                                <thead>
                                <tr>
                                    <!-- Set columns width -->
                                    <th class="text-center py-3 px-4" style="min-width: 400px;">Books & Details</th>
                                    <th class="text-center py-3 px-4" style="width: 100px;">Total Price</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($books as $bookId => $bookDetails) {
                                    $totalPrice += $bookDetails['price'] * $bookDetails['quantity'];
                                    ?>
                                    <tr>
                                        <td class="p-3">
                                            <div class="media align-items-center">
                                                <img src="../../img/books/<?php echo htmlspecialchars($bookId); ?>.jpg"
                                                     class="d-block ui-w-40 ui-bordered mr-4"
                                                     style="width: 20%; height: auto;" alt="Book Image">
                                                <div class="media-body">
                                                    <a href="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/bookDetails.php?book_id=' . $bookId); ?>"
                                                       class="d-block text-dark"><?= $bookDetails['title'] ?></a>
                                                    <small>
                                                        <span class="text-muted">Author: </span> <?= $bookDetails['author'] ?>
                                                        &nbsp;<br>
                                                        <span class="text-muted">Publisher: </span> <?= $bookDetails['publisher'] ?>
                                                        &nbsp;<br>
                                                        <span class="text-muted">Quantity: </span> <?= $bookDetails['quantity'] ?>
                                                        <br>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center font-weight-semibold align-middle p-5">
                                            $<?= $bookDetails['price'] * $bookDetails['quantity'] ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    } else {
                        ?>
                        <h4>No books to show in the shopping cart</h4>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Order Summary</h5>
                    <hr>

                    <!-- Total Price -->
                    <div class="mb-3">
                        <h5 class="mb-1">Total Amount</h5>
                        <p class="mb-0"><?php echo htmlspecialchars('$' . $totalPrice); ?></p>
                    </div>

                    <hr>
                    <!-- Address Information -->
                    <div class="mb-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5>Shipping Info</h5>
                            </div>
                            <div class="col-md-4">
                                <form action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/util/changeInfoCheckout.php'); ?>"
                                      method="POST">
                                    <input type="hidden" name="editInfo" value="shippingInfo">
                                    <!-- Hidden token to protect against XSRF -->
                                    <input type="hidden" name="token"
                                           value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">
                                    <button type="submit" class="btn btn-secondary btn-sm ml-1"><i
                                                class="fas fa-edit"></i></button>
                                </form>
                            </div>
                        </div>

                        <small>
                            <?php
                            // Displays the shipping info
                            $shippingInfo = $_SESSION['shippingInfo'];

                            foreach ($_SESSION['shippingInfo'] as $key => $value) {
                                ?>
                                <span><?php echo htmlspecialchars($_SESSION['shippingInfo'][$key]); ?></span><br>
                                <?php
                            }
                            ?>
                        </small>
                    </div>

                    <hr>
                    <!-- Credit Card Information -->
                    <div class="mb-3">
                        <div class="row align-items-center mb-3">
                            <div class="col-md-8">
                                <h5>Payment Method</h5>
                            </div>
                            <div class="col-md-4">
                                <form action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/util/changeInfoCheckout.php'); ?>"
                                      method="POST">
                                    <input type="hidden" name="editInfo" value="paymentInfo">
                                    <!-- Hidden token to protect against CSRF -->
                                    <input type="hidden" name="token"
                                           value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">
                                    <button type="submit" class="btn btn-secondary btn-sm ml-1"><i
                                                class="fas fa-edit"></i></button>
                                </form>

                            </div>
                        </div>
                        <p class="mb-0">Credit Card
                            Number: <?php echo htmlspecialchars('****' . substr($_SESSION['paymentInfo']['cardNumber'], -4)); ?></p>
                    </div>

                    <!-- Checkout Button -->
                    <form action="//<?php echo htmlspecialchars(SERVER_ROOT . '/php/user/orderSummary.php'); ?>"
                          method="POST">
                        <?php
                        if (!empty($books)) {
                            ?>
                            <input type="hidden" name="totalPrice" value="<?php echo htmlspecialchars($totalPrice); ?>">
                            <!-- Hidden token to protect against CSRF -->
                            <input type="hidden" name="token"
                                   value="<?php echo htmlspecialchars($_SESSION['token'] ?? ''); ?>">
                            <button type="submit" class="btn btn-primary btn-block">Checkout</button>
                            <?php
                        } else {
                            ?>
                            <p>No books in the shopping cart.</p>
                            <?php
                        }
                        ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
</html>