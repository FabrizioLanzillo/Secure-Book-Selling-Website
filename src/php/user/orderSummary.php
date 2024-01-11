<?php
require_once __DIR__ . "/../../config.php";

global $sessionHandler;
global $shoppingCartHandler;
global $errorHandler;
global $accessControlManager;

// This function checks if the user has already inserted payment info and shipping info
// if is not the case, the user will be redirected to the shopping cart page.
$accessControlManager->checkFinalStepCheckout();

$items = $shoppingCartHandler->getBooks();
$totalPrice = 0;

?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Order Summary Page</title>
        <link rel="stylesheet" href="../../css/bootstrap.css">
    </head>
    <body>
        <?php
        include "./../layout/header.php";
        ?>
        <section class="container mt-5 p-5 bg-info rounded ">
            <h2 class="mb-4">Checkout</h2>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h2>Order</h2>
                        </div>
                        <div class="card-body">
                            <?php
                            if ($items !== null) {
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
                                        foreach ($items as $itemId => $itemDetails) {
                                            $totalPrice += $itemDetails['price'] * $itemDetails['quantity'];
                                            ?>
                                            <tr>
                                                <td class="p-3">
                                                    <div class="media align-items-center">
                                                        <img src="../../img/books/<?php echo $itemId?>.jpg" class="d-block ui-w-40 ui-bordered mr-4" style="width: 20%; height: auto;" alt="Book Image">
                                                        <div class="media-body">
                                                            <a href="#" class="d-block text-dark"><?= $itemDetails['title'] ?></a>
                                                            <small>
                                                                <span class="text-muted">Author: </span> <?= $itemDetails['author'] ?>
                                                                &nbsp;<br>
                                                                <span class="text-muted">Publisher: </span> <?= $itemDetails['publisher'] ?>
                                                                &nbsp;<br>
                                                                <span class="text-muted">Quantity: </span> <?= $itemDetails['quantity'] ?><br>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center font-weight-semibold align-middle p-5">
                                                    $<?= $itemDetails['price'] * $itemDetails['quantity'] ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php
                            }
                            else {
                                ?>
                                <h4>No items to show in the shopping cart</h4>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Order Summary</h5><hr>

                            <!-- Total Price -->
                            <div class="mb-3">
                                <h5 class="mb-1">Total Amount</h5>
                                <p class="mb-0"><?php echo '$'. $totalPrice?></p>
                            </div>

                            <hr>
                            <!-- Address Information -->
                            <div class="mb-3">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5>Shipping Info</h5>
                                    </div>
                                    <div class="col-md-4">
                                        <form action="//<?php echo SERVER_ROOT . '/php/util/changeInfoCheckout.php' ?>" method="POST">
                                            <input type="hidden" name="editInfo" value="shippingInfo">
                                            <button type="submit" class="btn btn-secondary btn-sm mr-1">Edit</button>
                                        </form>
                                    </div>
                                </div>

                                <small>
                                        <?php
                                        $shippingInfo = $_SESSION['shippingInfo'];

                                        foreach ($_SESSION['shippingInfo'] as $key => $value) {
                                        ?>
                                            <span><?php echo $_SESSION['shippingInfo'][$key];?></span><br>
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
                                        <form action="//<?php echo SERVER_ROOT . '/php/util/changeInfoCheckout.php' ?>" method="POST">
                                            <input type="hidden" name="editInfo" value="paymentInfo">
                                            <button type="submit" class="btn btn-secondary btn-sm mr-1">Edit</button>
                                        </form>

                                    </div>
                                </div>
                                <p class="mb-0">Credit Card Number: <?php echo '****' . substr($_SESSION['paymentInfo']['cardNumber'], -4);?></p>
                            </div>

                            <!-- Checkout Button -->
                                <!-- TODO-->
<!--                            <form action="//--><?php //echo SERVER_ROOT . '/php/user/orderSummary.php' ?><!--" method="POST">-->
<!--                                <input type="hidden" name="editInfo" value="paymentInfo">-->
<!--                                <button type="submit" class="btn btn-primary btn-block">Pay Now</button>-->
<!--                            </form>-->
                            <a href="//<?php echo SERVER_ROOT . '/' ?>" target="_blank">
                                <button type="button" class="btn btn-primary btn-block">Pay Now</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>
</html>